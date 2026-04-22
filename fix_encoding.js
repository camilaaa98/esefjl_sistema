const fs = require('fs');
const path = require('path');

const dir = 'c:\\wamp64\\www\\YUDI_CONSTANZA\\farmacia\\esefjl\\docs';

const replacements = {
    '&iacute;±': 'ñ',
    '&iacute;³n': 'ón',
    '&iacute;³': 'ó',
    '&iacute;ºn': 'ún',
    '&iacute;­': 'í',
    '&aacute;': 'á',
    '&eacute;': 'é',
    '&iacute;': 'í',
    '&oacute;': 'ó',
    '&uacute;': 'ú',
    '&ntilde;': 'ñ',
    'â€”': '-',
    'â€"': '-',
    '"&iacute; rea': '"Área',
    '&iacute; rea': 'Área',
    'âš ï¸': '⚠️',
    'âœ…': '✅',
    'ðŸ›‘': '🛑',
    'â€œ': '"',
    'â€': '"',
    'R&eacute;gimen': 'Régimen',
    'Est&aacute;ndar': 'Estándar',
    '&Aacute;': 'Á',
    '&Eacute;': 'É',
    '&Iacute;': 'Í',
    '&Oacute;': 'Ó',
    '&Uacute;': 'Ú',
    '&Ntilde;': 'Ñ',
    '¡ndares': 'ándares',
    'Funci&iacute;³n': 'Función',
    'Semaforizaci&iacute;³n': 'Semaforización',
    'Dispensaci&iacute;³n': 'Dispensación',
    'Aprobaci&iacute;³n': 'Aprobación',
    'Atenci&iacute;³n': 'Atención',
    'Vinculaci&iacute;³n': 'Vinculación',
    'Informaci&iacute;³n': 'Información',
    'reposici&iacute;³n': 'reposición',
    'intervenci&iacute;³n': 'intervención',
    'prescripci&iacute;³n': 'prescripción',
    'T&eacute;cnica': 'Técnica',
    'autom&aacute;tico': 'automático',
    'f&iacute;­sico': 'físico',
    'f&iacute;­sica': 'física',
    'din&aacute;micas': 'dinámicas',
    'an&aacute;lisis': 'análisis',
    'seg&iacute;ºn': 'según',
    'd&iacute;­as': 'días',
    'err&iacute;³neo': 'erróneo',
    'autom&aacute;ticamente': 'automáticamente',
    'cl&iacute;­nico': 'clínico',
    'log&iacute;­sticos': 'logísticos'
};

function fixEncoding(filePath) {
    let content = fs.readFileSync(filePath, 'utf8');
    
    for (const [bad, good] of Object.entries(replacements)) {
        // global replace
        content = content.split(bad).join(good);
    }
    
    // Hardcode footer fix just in case it wasn't matched fully
    if(content.includes('Empresa Social del Estado Fabio Jaramillo Londoño')) {
        content = content.replace(/<p>&copy; 2026 Empresa Social del Estado Fabio Jaramillo Londo.*?<\/p>/gi, 
            '<p>&copy; 2026 Empresa Social del Estado Fabio Jaramillo Londoño (ESEFJL). Elaborado por la investigadora Camila Guevara.</p>');
    }
    
    fs.writeFileSync(filePath, content, 'utf8');
    console.log(`Fixed: ${path.basename(filePath)}`);
}

function walk(dirPath) {
    const files = fs.readdirSync(dirPath);
    for (const file of files) {
        const fullPath = path.join(dirPath, file);
        if (fs.statSync(fullPath).isDirectory()) {
            walk(fullPath);
        } else if (fullPath.endsWith('.html')) {
            fixEncoding(fullPath);
        }
    }
}

walk(dir);
