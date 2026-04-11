import os
import subprocess

def render_with_pitch_correction(video_path, audio_path, output_path, v_dur, a_dur):
    # El audio es 1.346 mas largo. Necesitamos que sea 1.346 mas rapido (atempo=1.346)
    # ffmpeg atempo solo acepta entre 0.5 y 2.0. 1.346 esta en rango.
    speed = a_dur / v_dur
    print(f"Sincronizando con preservaci\u00f3n de tono (Pitch Correction). Velocidad: {speed}")
    
    # Usar ffmpeg directamente para el filtro atempo (preserva el tono natural de la voz)
    # Comando: video + audio -> [atempo] -> result
    cmd = [
        "scratch/ffmpeg.exe", "-y",
        "-i", video_path,
        "-i", audio_path,
        "-filter_complex", f"[1:a]atempo={speed}[aout]",
        "-map", "0:v",
        "-map", "[aout]",
        "-c:v", "libx264",
        "-preset", "fast",
        "-c:a", "aac",
        "-shortest",
        output_path
    ]
    
    try:
        subprocess.run(cmd, check=True)
        print(f"✅ FINALIZADO: {output_path}")
    except Exception as e:
        print(f"❌ ERROR: {str(e)}")

if __name__ == "__main__":
    base = "c:/wamp64/www/YUDI_CONSTANZA/farmacia/esefjl/img/"
    render_with_pitch_correction(
        os.path.join(base, "sheck.mp4"),
        os.path.join(base, "2.ogg"),
        os.path.join(base, "sombreado.mp4"),
        79.55, 107.11
    )
