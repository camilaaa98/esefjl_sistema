import os
from moviepy import VideoFileClip, AudioFileClip, vfx

def render_sync_pro_fixed(video_path, audio_path, output_path, speed):
    print(f"Iniciando Renderizaci\u00f3n de Alta Precisi\u00f3n (Speed: {speed})")
    try:
        video = VideoFileClip(video_path)
        audio = AudioFileClip(audio_path)
        
        # Acelerar audio para coincidir EXACTAMENTE con el video
        # En MoviePy 2.x, para acelerar solo el audio usamos with_effects con afx si es audio, 
        # pero MultiplySpeed en vfx a veces funciona para clips de audio.
        # Probemos con el efecto gen\u00e9rico multiply_speed si est\u00e1 disponible o import\u00e9moslo.
        
        print(f"Sincronizando audio al ritmo del video...")
        audio_sync = audio.with_effects([vfx.MultiplySpeed(speed)])
        
        # Mezclar
        video_final = video.with_audio(audio_sync)
        
        # Guardar
        print(f"Exportando: {output_path}")
        video_final.write_videofile(output_path, codec="libx264", audio_codec="aac")
        print("\u2705 Sincronizaci\u00f3n Exitosa")
        
    except Exception as e:
        print(f"ERROR: {str(e)}")

if __name__ == "__main__":
    base = "c:/wamp64/www/YUDI_CONSTANZA/farmacia/esefjl/img/"
    v_p = os.path.join(base, "sheck.mp4")
    a_p = os.path.join(base, "2.ogg")
    o_p = os.path.join(base, "sombreado.mp4")
    
    # Velocidad calculada: 107.11 / 79.55 = 1.34645
    render_sync_pro_fixed(v_p, a_p, o_p, 1.34645)
