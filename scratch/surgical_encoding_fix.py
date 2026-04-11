import os

replacements = {
    'Ã¡': '&aacute;',
    'Ã©': '&eacute;',
    'Ã-': '&iacute;', # common error for í
    'Ã³': '&oacute;',
    'Ãº': '&uacute;',
    'Ã±': '&ntilde;',
    'Ã‘': '&Ntilde;',
    'Â¿': '&iquest;',
    'Â¡': '&iexcl;',
    'Ã': '&iacute;', # catch-all for lone í mojibake
}

def fix_mojibake(filepath):
    if not os.path.exists(filepath):
        return
    with open(filepath, 'r', encoding='latin-1') as f: # Likely read as latin-1 if it has mojibake
        content = f.read()
    
    for old, new in replacements.items():
        content = content.replace(old, new)
        
    # Ensure meta charset exists
    if '<meta charset="UTF-8">' not in content:
        content = content.replace('<head>', '<head>\n    <meta charset="UTF-8">')
    
    with open(filepath, 'w', encoding='utf-8') as f:
        f.write(content)
    print(f"Fixed {filepath}")

docs = [
    'c:/wamp64/www/YUDI_CONSTANZA/farmacia/esefjl/docs/articulo_cientifico.html',
    'c:/wamp64/www/YUDI_CONSTANZA/farmacia/esefjl/docs/manual_usuario.html',
    'c:/wamp64/www/YUDI_CONSTANZA/farmacia/esefjl/docs/manual_tecnico.html',
    'c:/wamp64/www/YUDI_CONSTANZA/farmacia/esefjl/docs/articulo_cientifico_en.html'
]

for doc in docs:
    fix_mojibake(doc)
