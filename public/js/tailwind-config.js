tailwind.config = {
    darkMode: 'class',
    theme: {
        extend: {
            colors: {
                medical: {
                    50: '#f0f9f8',
                    100: '#d9f0ee',
                    500: '#006D5B', // Verde Médico Principal
                    600: '#00594a',
                    700: '#00463b',
                },
                base: {
                    dark: '#0f172a', // Azul Oscuro Profesional
                    light: '#ffffff'
                }
            },
            fontFamily: {
                sans: ['Inter', 'sans-serif'],
            }
        }
    }
}
