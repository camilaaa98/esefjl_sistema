import os
from moviepy import VideoFileClip, AudioFileClip, ColorClip, CompositeVideoClip
import moviepy.video.fx as fx

def merge_audio_video_v2(video_path, audio_path, output_path):
    print(f"Iniciando Render V2 (Profesional con Sombreado): {audio_path}")
    
    if not os.path.exists(video_path) or not os.path.exists(audio_path):
        print("Error: Archivos no encontrados.")
        return

    try:
        # 1. Cargar Video y Audio
        video = VideoFileClip(video_path)
        audio = AudioFileClip(audio_path)
        
        # 2. Aplicar Sombreado (Efecto Viñeta/Vignette)
        # En MoviePy 2.x, fx se aplica directamente o mediante vfx
        video_sombreado = video.fx(fx.vignette, x_apex=None, y_apex=None, radius=0.6, intensity=0.7)
        
        # 3. Mezclar con el nuevo audio
        video_final = video_sombreado.with_audio(audio)
        
        # 4. Renderizar
        print("Renderizando video con efectos institucionales...")
        video_final.write_videofile(output_path, codec="libx264", audio_codec="aac", temp_audiofile='temp-audio.m4a', remove_temp=True)
        
        print(f"EXITO: Video generado en {output_path}")
        
    except Exception as e:
        print(f"ERROR: {str(e)}")

if __name__ == "__main__":
    base_path = "c:/wamp64/www/YUDI_CONSTANZA/farmacia/esefjl/img/"
    merge_audio_video_v2(
        os.path.join(base_path, "sheck.mp4"),
        os.path.join(base_path, "2.ogg"),
        os.path.join(base_path, "manual_v2_profesional.mp4")
    )
