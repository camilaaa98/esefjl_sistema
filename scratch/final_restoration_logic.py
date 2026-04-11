import subprocess
import os

def restore_file(commit, path):
    try:
        # Get raw bytes from git
        raw_bytes = subprocess.check_output(['git', 'show', f'{commit}:{path}'], stderr=subprocess.STDOUT)
        
        # Try to decode as UTF-16 (seems common in this project)
        try:
            content = raw_bytes.decode('utf-16')
            print(f"Decoded {path} as UTF-16")
        except UnicodeDecodeError:
            try:
                content = raw_bytes.decode('utf-8')
                print(f"Decoded {path} as UTF-8")
            except UnicodeDecodeError:
                content = raw_bytes.decode('latin-1', errors='replace')
                print(f"Decoded {path} as Latin-1 (Fallback)")
        
        # Fix mojibake characters to HTML entities surgically
        replacements = {
            'Ã¡': '&aacute;', 'Ã©': '&eacute;', 'Ã-': '&iacute;', 'Ã': '&iacute;',
            'Ã³': '&oacute;', 'Ãº': '&uacute;', 'Ã±': '&ntilde;', 'Ã‘': '&Ntilde;',
            'Â¿': '&iquest;', 'Â¡': '&iexcl;',
            'Ã³': '&oacute;'
        }
        for old, new in replacements.items():
            content = content.replace(old, new)
            
        # Write back as UTF-8 without BOM
        target_path = os.path.join('c:/wamp64/www/YUDI_CONSTANZA/farmacia/esefjl/', path)
        with open(target_path, 'w', encoding='utf-8') as f:
            f.write(content)
        print(f"Restored {path} to {target_path}")
        
    except Exception as e:
        print(f"Failed to restore {path}: {str(e)}")

commit_id = '1753ebd'
files_to_restore = [
    'docs/articulo_cientifico.html',
    'docs/manual_usuario.html',
    'docs/manual_tecnico.html',
    'docs/articulo_cientifico_en.html'
]

for f in files_to_restore:
    restore_file(commit_id, f)
