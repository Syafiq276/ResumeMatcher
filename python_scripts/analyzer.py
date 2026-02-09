import argparse
import json
import sys
import os
import re
import math
from typing import List, Dict

# Third-party imports (wrapped in try-except for graceful error handling if dependencies missing during skeletal run)
try:
    import pdfplumber
    from docx import Document
    from bs4 import BeautifulSoup
    import requests
    from sklearn.feature_extraction.text import TfidfVectorizer
    from sklearn.metrics.pairwise import cosine_similarity
    from google import genai
    from dotenv import load_dotenv
    import nltk
    from nltk.corpus import stopwords
    from nltk.tokenize import word_tokenize
except ImportError as e:
    # If called from Laravel without venv, this might fail.
    # But we expect venv to be used.
    print(json.dumps({"error": f"Missing dependency: {str(e)}"}))
    sys.exit(1)

# Load environment variables (from Laravel .env or local .env)
# Assuming .env is in the parent directory of python_scripts (root of laravel)
load_dotenv(os.path.join(os.path.dirname(__file__), '../.env'))

# Constants
GEMINI_API_KEY = os.getenv('GEMINI_API_KEY')
OPENAI_API_KEY = os.getenv('OPENAI_API_KEY')

# Ensure NLTK data
nltk_data_path = os.getenv('NLTK_DATA')
if nltk_data_path:
    nltk.data.path.append(nltk_data_path)

try:
    nltk.data.find('tokenizers/punkt')
    nltk.data.find('corpora/stopwords')
    nltk.data.find('tokenizers/punkt_tab')
except LookupError:
    # Download to the specified NLTK_DATA directory if available, else default
    download_dir = nltk_data_path if nltk_data_path else None
    nltk.download('punkt', download_dir=download_dir, quiet=True)
    nltk.download('stopwords', download_dir=download_dir, quiet=True)
    nltk.download('punkt_tab', download_dir=download_dir, quiet=True)

def extract_text_from_pdf(file_path):
    text = ""
    with pdfplumber.open(file_path) as pdf:
        for page in pdf.pages:
            text += page.extract_text() or ""
    return text

def extract_text_from_docx(file_path):
    doc = Document(file_path)
    return "\n".join([para.text for para in doc.paragraphs])

def clean_text(text):
    # Lowercase
    text = text.lower()
    # Remove special chars but keep spaces
    text = re.sub(r'[^a-z0-9\s]', '', text)
    # Remove stopwords
    stop_words = set(stopwords.words('english'))
    word_tokens = word_tokenize(text)
    filtered_text = [w for w in word_tokens if not w in stop_words]
    return " ".join(filtered_text)

def calculate_cosine_similarity(text1, text2):
    # Returns percentage 0-100
    if not text1 or not text2:
        return 0.0
    
    tfidf_vectorizer = TfidfVectorizer()
    try:
        tfidf_matrix = tfidf_vectorizer.fit_transform([text1, text2])
        cosine_sim = cosine_similarity(tfidf_matrix[0:1], tfidf_matrix[1:2])
        return cosine_sim[0][0] * 100
    except ValueError:
        return 0.0

def get_llm_insight(resume_text, job_text, gap_score):
    if not GEMINI_API_KEY:
        return {
            "key_matches": [],
            "missing_skills": [],
            "resume_optimization_tips": ["LLM API Key not configured."],
            "tnb_specific_insight": "N/A",
            "match_score": 0
        }

    client = genai.Client(api_key=GEMINI_API_KEY)
    
    prompt = f"""
    You are an expert Career Coach and Resume Strategist. 
    Your goal is to help a candidate get hired by finding *potential* and offering *constructive, actionable* advice, even if the resume isn't perfect.
    
    Resume Content:
    {resume_text[:4000]}... (truncated)
    
    Job Description:
    {job_text[:4000]}... (truncated)
    
    Current Vector/Keyword Match: {gap_score:.1f}% (This is a raw mathematical score, use your judgment to adjust).
    
    Task:
    1. Analyze the Semantic Match: Does the candidate have the core transferable skills, even if exact keywords are missing?
    2. Provide a 'match_score' (0-100) based on your expert assessment of their fit. Be generous if they show promise.
    3. Generate specific, actionable tips to close the gap.
    
    Return ONLY a JSON object with this structure:
    {{
        "match_score": 75,  // Integer 0-100. Be fair but encouraging.
        "key_matches": ["list", "of", "strong", "points", "or", "matched", "skills"],
        "missing_skills": ["list", "of", "missing", "critical", "skills"],
        "resume_optimization_tips": ["Actionable Tip 1 (e.g., 'Add a project using X')", "Tip 2", "Tip 3"],
        "tnb_specific_insight": "A motivational specific insight about why they might be a good fit (or not) and one strategic move to make."
    }}
    """
    
    try:
        response = client.models.generate_content(model='gemini-2.0-flash', contents=prompt)
        content = response.text.strip()
        
        # Clean up if the model adds ```json ... ```
        if content.startswith('```'):
            content = content.replace('```json', '').replace('```', '')
            content = content.replace('json', '') 
        
        result = json.loads(content)
        return result
    except Exception as e:
        return {
            "key_matches": [],
            "missing_skills": [],
            "resume_optimization_tips": [f"AI Error: {str(e)}"],
            "tnb_specific_insight": "Error generating insight.",
            "match_score": 0
        }

def hard_skills_match(resume_text, job_text):
    # Simple lexicon matching for common tech skills
    # This could be expanded significantly
    common_skills = ['python', 'java', 'laravel', 'php', 'javascript', 'react', 'vue', 'sql', 'docker', 'aws', 'pandas', 'scikit-learn']
    
    resume_words = set(clean_text(resume_text).split())
    job_words = set(clean_text(job_text).split())
    
    job_skills = {skill for skill in common_skills if skill in job_words}
    if not job_skills:
        return 100.0 # No hard skills detected in job ad to match against
        
    matched_skills = job_skills.intersection(resume_words)
    
    if not job_skills:
        return 0.0
    
    return (len(matched_skills) / len(job_skills)) * 100

def main():
    parser = argparse.ArgumentParser(description='Resume Matcher Analysis')
    parser.add_argument('--resume', required=True, help='Path to resume file')
    parser.add_argument('--job_file', required=True, help='Path to text file containing job description')
    
    args = parser.parse_args()
    
    resume_path = args.resume
    job_file_path = args.job_file
    
    # 1. Read files
    try:
        with open(job_file_path, 'r', encoding='utf-8') as f:
            job_text_raw = f.read()
            
        file_ext = os.path.splitext(resume_path)[1].lower()
        if file_ext == '.pdf':
            resume_text_raw = extract_text_from_pdf(resume_path)
        elif file_ext == '.docx':
            resume_text_raw = extract_text_from_docx(resume_path)
        else:
            print(json.dumps({"error": "Unsupported file format"}))
            sys.exit(1)
            
    except Exception as e:
        print(json.dumps({"error": f"File reading error: {str(e)}"}))
        sys.exit(1)

    # 2. Preprocess
    resume_clean = clean_text(resume_text_raw)
    job_clean = clean_text(job_text_raw)
    
    # 3. Analysis
    vector_match = calculate_cosine_similarity(resume_clean, job_clean)
    lexicon_match = hard_skills_match(resume_text_raw, job_text_raw)
    
    # 4. LLM Feedback
    # Pass vector_match as a baseline for the LLM to consider
    llm_data = get_llm_insight(resume_text_raw, job_text_raw, vector_match)
    
    llm_score = llm_data.get('match_score', 0)
    
    # Hybrid Score Calculation
    # We trust the LLM (semantic understanding) more than raw vector match
    # Formula: 60% LLM + 30% Vector + 10% Lexicon
    # If LLM score is 0 (error), fallback to old formula
    if llm_score > 0:
        final_score = (llm_score * 0.6) + (vector_match * 0.3) + (lexicon_match * 0.1)
    else:
        final_score = (vector_match * 0.7) + (lexicon_match * 0.3)
    
    # Cap at 100
    final_score = min(final_score, 100.0)
    
    # Boost Logic: If score is still unreasonably low (<40) but LLM saw potential (>60), reasonable boost
    if final_score < 40 and llm_score > 60:
        final_score = (final_score + llm_score) / 2

    # 5. Determine Eligibility Status
    if final_score >= 75:
        eligibility = "Perfect Match"
    elif final_score >= 60:
        eligibility = "Strong Candidate"
    elif final_score >= 40:
        eligibility = "Potential Match"
    else:
        eligibility = "Needs Improvement"
    
    result = {
        "match_percentage": round(final_score, 1),
        "lexicon_match": round(lexicon_match, 1),
        "vector_match": round(vector_match, 1),
        "eligibility_status": eligibility,
        "key_matches": llm_data.get('key_matches', []),
        "missing_skills": llm_data.get('missing_skills', []),
        "resume_optimization_tips": llm_data.get('resume_optimization_tips', []),
        "tnb_specific_insight": llm_data.get('tnb_specific_insight', "N/A")
    }
    
    print(json.dumps(result))

if __name__ == "__main__":
    main()
