import os
from moviepy import VideoFileClip, AudioFileClip

def render_final_video(video_path, audio_path, output_path):
    print(f"Renderizando Video Final: {output_path}")
    try:
        video = VideoFileClip(video_path)
        audio = AudioFileClip(audio_path)
        
        video_final = video.with_audio(audio)
        video_final.write_videofile(output_path, codec="libx264", audio_codec="aac")
        
        print(f"✅ FINALIZADO: {output_path}")
    except Exception as e:
        print(f"❌ ERROR: {str(e)}")

if __name__ == "__main__":
    base = "c:/wamp64/www/YUDI_CONSTANZA/farmacia/esefjl/img/"
    render_final_video(
        os.path.join(base, "sheck.mp4"),
        os.path.join(base, "2.ogg"),
        os.path.join(base, "sombreado.mp4")
    )
