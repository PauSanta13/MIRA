FROM python:3.12-slim

WORKDIR /app

RUN pip install --no-cache-dir requests

COPY scanner.py .

ENV PYTHONUNBUFFERED=1

ENTRYPOINT ["python", "scanner.py"]
