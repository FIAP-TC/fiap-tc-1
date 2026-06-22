## Como iniciar o projeto

1. Configurar as variáveis de ambiente
```bash
cp .env.example .env
```

2. Construir e iniciar os containers
```yml
docker compose up -d --build
```

O projeto estará totalmente disponível para consumo em: http://localhost:9000