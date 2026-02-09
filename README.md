# Resume Matcher 🚀

**Resume Matcher** is an intelligent career assistant that helps job seekers align their resumes with job descriptions using advanced AI. By combining vector-based semantic analysis with LLM-powered judgment, it acts as a personal "Career Coach," offering actionable optimization tips to beat Applicant Tracking Systems (ATS).

## ✨ Key Features

- **🧠 AI-Powered Analysis**: Uses **Google Gemini 2.0** to understand the nuance of your Experience vs. Job Requirements.
- **📊 Hybrid Scoring System**:
  - **Vector Match (30%)**: Mathematical cosine similarity for semantic grounding.
  - **LLM Assessment (60%)**: "Human-like" evaluation of transferable skills and potential.
  - **Keyword Match (10%)**: Hard skills verification.
- **📄 Resume Parsing**: Supports **PDF** and **DOCX** formats.
- **💡 Actionable Feedback**: Get specific, constructive tips to improve your resume (not just "add keywords").
- **📂 Scan History**: Track your past analyses and revisit detailed reports.
- **🔒 Secure Accounts**: User authentication powered by **Laravel Breeze**.
- **🎨 Modern UI**: Sleek, responsive design built with **Tailwind CSS**.

## 🛠️ Tech Stack

- **Frontend**: [Laravel Blade](https://laravel.com/docs/blade), [Tailwind CSS](https://tailwindcss.com/)
- **Backend**: [Laravel 12](https://laravel.com/), PHP 8.2+
- **AI Engine**: Python 3.x, `google-genai`, `scikit-learn`, `nltk`
- **Database**: SQLite / MySQL

## 🚀 Getting Started

### Prerequisites

- PHP 8.2+
- Composer
- Node.js & NPM
- Python 3.10+
- Gemini API Key

### Installation

1.  **Clone the Repository**
    ```bash
    git clone https://github.com/yourusername/resume-matcher.git
    cd resume-matcher
    ```

2.  **Install PHP Dependencies**
    ```bash
    composer install
    ```

3.  **Install Frontend Dependencies**
    ```bash
    npm install && npm run build
    ```

4.  **Setup Environment**
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```
    *Configure your database (DB_CONNECTION) and add your `GEMINI_API_KEY` in `.env`.*

5.  **Setup Python Environment**
    ```bash
    python -m venv venv
    source venv/bin/activate  # Windows: venv\Scripts\activate
    pip install -r requirements.txt
    ```

6.  **Run Migrations**
    ```bash
    php artisan migrate
    ```

7.  **Serve Application**
    ```bash
    php artisan serve
    ```

## 📝 License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
