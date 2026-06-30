# Deploy do ShareHub (Supabase + Render)

## 1. Criar o projeto no Supabase

1. Acesse https://supabase.com e crie uma nova conta/projeto.
2. Anote a senha do banco definida na criação do projeto.
3. Em **Settings > Database > Connection info**, copie:
   - Host (algo como `db.xxxxxxxxxxxx.supabase.co`)
   - Port (5432)
   - Database name (`postgres`)
   - User (`postgres`)
   - Password (a que você definiu)
4. Em **Settings > Storage > S3 Connection** (se for usar Supabase Storage para anexos):
   - Access Key ID
   - Secret Access Key
   - Endpoint (algo como `https://xxxxxxxxxxxx.supabase.co/storage/v1/s3`)
   - Region (geralmente `us-east-1` ou a região do projeto)
5. Crie um bucket de Storage (ex: `sharehub`) caso vá usar anexos de recibos.

## 2. Preencher o .env

Copie `.env.example` para `.env` e preencha:

```
DB_CONNECTION=pgsql
DB_HOST=db.xxxxxxxxxxxx.supabase.co
DB_PORT=5432
DB_DATABASE=postgres
DB_USERNAME=postgres
DB_PASSWORD=sua_senha

FILESYSTEM_DISK=s3
AWS_ACCESS_KEY_ID=...
AWS_SECRET_ACCESS_KEY=...
AWS_DEFAULT_REGION=us-east-1
AWS_BUCKET=sharehub
AWS_ENDPOINT=https://xxxxxxxxxxxx.supabase.co/storage/v1/s3
AWS_USE_PATH_STYLE_ENDPOINT=true
```

Gere a `APP_KEY` localmente com `php artisan key:generate` e copie o valor para o `.env` (e depois para as variáveis de ambiente no Render).

## 3. Conectar o repositório ao Render

1. Faça push deste repositório para o GitHub.
2. No painel do Render (https://dashboard.render.com), clique em **New > Web Service**.
3. Selecione o repositório. Render deve detectar o `render.yaml` na raiz do projeto automaticamente (Blueprint).
4. Caso prefira configurar manualmente em vez do Blueprint:
   - Build Command: `composer install --no-dev --optimize-autoloader && npm install && npm run build && php artisan config:cache`
   - Start Command: `php artisan serve --host=0.0.0.0 --port=$PORT`

## 4. Variáveis de ambiente no Render

No painel do serviço, em **Environment**, defina (os mesmos valores do seu `.env`):

- `APP_NAME=ShareHub`
- `APP_ENV=production`
- `APP_DEBUG=false`
- `APP_KEY` (o valor gerado por `php artisan key:generate --show`)
- `APP_URL` (a URL pública do serviço no Render)
- `DB_CONNECTION=pgsql`
- `DB_HOST`, `DB_PORT=5432`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` (do Supabase)
- `FILESYSTEM_DISK=s3`, `AWS_ACCESS_KEY_ID`, `AWS_SECRET_ACCESS_KEY`, `AWS_DEFAULT_REGION`, `AWS_BUCKET`, `AWS_ENDPOINT`, `AWS_USE_PATH_STYLE_ENDPOINT=true`

## 5. Comandos pós-deploy

Após o primeiro deploy bem-sucedido, rode (via Render Shell ou um job único):

```
php artisan migrate --force
php artisan db:seed --force
php artisan storage:link
```

## Observações

- `php artisan serve` é adequado para validar o deploy rapidamente, mas não é recomendado para produção em alta escala. Para produção robusta, considere migrar para `env: docker` no Render com uma imagem nginx + php-fpm.
- Os ícones do PWA (`public/images/icons/icon-192.png` e `icon-512.png`) ainda precisam ser substituídos por imagens reais — atualmente são apenas referenciados no `manifest.json`.
