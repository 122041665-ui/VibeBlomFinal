FROM python:3.11-slim

WORKDIR /app

COPY VibeBloomAdmin/requirements.txt .

RUN pip install --no-cache-dir -r requirements.txt

COPY VibeBloomAdmin/. .
COPY VibeBloom/public/images/vibebloom.png /app/app/static/images/vibebloom.png

EXPOSE 5010

CMD ["python", "run.py"]
