import os
from moviepy import VideoFileClip, AudioFileClip

def merge_audio_video(video_path, audio_path, output_path):
    print(f"Iniciando proceso de mezcla: {video_path} + {audio_path}")
    
    if not os.path.exists(video_path):
        print(f"Error: No se encuentra el video en {video_path}")
        return
    if not os.path.exists(audio_path):
        print(f"Error: No se encuentra el audio en {audio_path}")
        return

    try:
        # Cargar el video
        video = VideoFileClip(video_path)
        # Cargar el audio
        audio = AudioFileClip(audio_path)
        
        # Reemplazar el audio del video con el nuevo
        video_final = video.with_audio(audio)
        
        # Exportar el resultado
        print("Renderizando video final...")
        video_final.write_videofile(output_path, codec="libx264", audio_codec="aac")
        
        print(f"✅ ¡Video generado con éxito! Guardado en: {output_path}")
        
    except Exception as e:
        print(f"❌ Error durante el procesamiento: {str(e)}")

if __name__ == "__main__":
    base_path = "c:/wamp64/www/YUDI_CONSTANZA/farmacia/esefjl/img/"
    v_path = os.path.join(base_path, "sheck.mp4")
    a_path = os.path.join(base_path, "1.ogg")
    o_path = os.path.join(base_path, "manual_profesional.mp4")
    
    merge_audio_video(v_path, a_path, o_path)
