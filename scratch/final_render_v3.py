import os
from moviepy import VideoFileClip, AudioFileClip, vfx

def render_final_video_v3(video_path, audio_path, output_path, speed=1.12):
    print(f"Renderizando Video V3 (Audio acelerado {speed}x): {output_path}")
    try:
        video = VideoFileClip(video_path)
        audio = AudioFileClip(audio_path)
        
        # Acelerar el audio
        print(f"Optimizando ritmo de locución...")
        audio_fast = audio.with_effects([vfx.multiply_speed(speed)])
        
        video_final = video.with_audio(audio_fast)
        video_final.write_videofile(output_path, codec="libx264", audio_codec="aac")
        
        print(f"SUCCESS: {output_path}")
    except Exception as e:
        print(f"ERROR: {str(e)}")

if __name__ == "__main__":
    base = "c:/wamp64/www/YUDI_CONSTANZA/farmacia/esefjl/img/"
    render_final_video_v3(
        os.path.join(base, "sheck.mp4"),
        os.path.join(base, "2.ogg"),
        os.path.join(base, "sombreado.mp4"),
        speed=1.15  # 15% más rápido para compensar la lentitud
    )
