import os
from moviepy import VideoFileClip, AudioFileClip, vfx

def render_sync_pro(video_path, audio_path, output_path, speed):
    print(f"Iniciando Renderizaci\u00f3n de Alta Precisi\u00f3n (Speed: {speed})")
    try:
        video = VideoFileClip(video_path)
        audio = AudioFileClip(audio_path)
        
        # Acelerar audio para coincidir EXACTAMENTE con el video
        print(f"Calculando sincronizaci\u00f3n temporal...")
        audio_sync = audio.with_effects([vfx.multiply_speed(speed)])
        
        # Mezclar
        video_final = video.with_audio(audio_sync)
        
        # Guardar
        print(f"Exportando: {output_path}")
        video_final.write_videofile(output_path, codec="libx264", audio_codec="aac")
        print("\u2705 Sincronizaci\u00f3n Exitosa")
        
    except Exception as e:
        # Evitar caracteres unicode en el error para el terminal de windows
        print(f"ERROR: {str(e)}")

if __name__ == "__main__":
    base = "c:/wamp64/www/YUDI_CONSTANZA/farmacia/esefjl/img/"
    v_p = os.path.join(base, "sheck.mp4")
    a_p = os.path.join(base, "2.ogg")
    o_p = os.path.join(base, "sombreado.mp4")
    
    # Velocidad calculada: 107.11 / 79.55 = 1.34645
    render_sync_pro(v_p, a_p, o_p, 1.34645)
