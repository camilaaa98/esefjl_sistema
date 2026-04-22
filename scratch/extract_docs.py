import sys
from pypdf import PdfReader
from docx import Document

def extract_pdf(pdf_path, txt_out_path):
    try:
        reader = PdfReader(pdf_path)
        text = ""
        for page in reader.pages:
            text += page.extract_text() + "\n"
        with open(txt_out_path, "w", encoding="utf8") as f:
            f.write(text)
        print(f"Extracted PDF to {txt_out_path}")
    except Exception as e:
        print(f"PDF extraction failed: {e}")

def extract_docx(docx_path, txt_out_path):
    try:
        doc = Document(docx_path)
        text = ""
        for para in doc.paragraphs:
            text += para.text + "\n"
        with open(txt_out_path, "w", encoding="utf8") as f:
            f.write(text)
        print(f"Extracted DOCX to {txt_out_path}")
    except Exception as e:
        print(f"DOCX extraction failed: {e}")

if __name__ == "__main__":
    extract_pdf("C:\\wamp64\\www\\YUDI_CONSTANZA\\farmacia\\esefjl\\img\\investigacion\\COMPORTAMIENTO_ECONOMICO_FLORENCIA.pdf", "C:\\wamp64\\www\\YUDI_CONSTANZA\\farmacia\\esefjl\\scratch\\pdf_extractor.txt")
    extract_docx("C:\\wamp64\\www\\YUDI_CONSTANZA\\farmacia\\esefjl\\img\\emprendimiento\\CORRECCIONES ARTICULO GRUPO VOCERA.docx", "C:\\wamp64\\www\\YUDI_CONSTANZA\\farmacia\\esefjl\\scratch\\docx_extractor.txt")
