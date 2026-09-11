# Contexto do projeto fiap-tc-1

Este arquivo é lido automaticamente pelo Claude Code no início de toda sessão nova nesta pasta. Ele resume uma jornada longa de aprendizado prático (Docker → Kubernetes → Terraform/AWS → CI/CD) pra que qualquer conversa nova continue com o contexto certo, sem precisar reexplicar tudo.

## O que é o projeto

API Laravel (PHP 8.3) de gestão de oficina mecânica — Tech Challenge Fase 1 da pós FIAP. Documentação funcional/domínio no `README.md`. Documentação de infraestrutura completa (arquitetura, Terraform, Kubernetes, CI/CD) em [`docs/INFRAESTRUTURA.md`](docs/INFRAESTRUTURA.md) — **leia esse arquivo primeiro** pra qualquer dúvida técnica de infra, ele é a fonte da verdade atualizada.

## Como o usuário gosta de trabalhar (importante, ver [[feedback_hands_on_learning]])

- Isso é um exercício de aprendizado prático, não um trabalho delegado. **Não escreva manifests/arquivos `.tf` nem rode comandos que mudam estado real (kubectl apply/create/delete, terraform apply/destroy, aws cli mutante) sem antes explicar e pedir pra ele rodar** — isso vale pro cluster local, pro cluster AWS e pro Terraform, sem exceção.
- Comandos de leitura/diagnóstico (`kubectl get/describe/logs/top`, `terraform state list/plan`, `aws ... describe/list/get`) são OK rodar direto pra investigar e verificar.
- Nunca entre com credenciais/API keys/tokens em nome dele (nem `aws configure`, nem editar `~/.aws/credentials` com os valores reais) — é uma regra de segurança, não desconfiança.
- Explicações devem ser bem didáticas — o usuário se descreve como não-expert, quer entender o "porquê", não só copiar comando.

## Estado atual da infraestrutura

- **AWS**: geralmente desligada entre sessões de estudo (zero custo) — confirmar sempre com uma varredura antes de assumir algo rodando: `aws eks list-clusters`, `aws ec2 describe-instances`, `aws elbv2 describe-load-balancers`, `aws ec2 describe-volumes/addresses/nat-gateways`. A conta é AWS Academy Learner Lab — **credenciais expiram por sessão**, sempre confirmar com `aws sts get-caller-identity` antes de qualquer diagnóstico.
- **Cluster local** (Docker Desktop, contexto `docker-desktop`, cluster tipo `kind`, 4 nodes) fica normalmente de pé — `kubectl apply -k k8s/overlays/local` sobe tudo.
- Bucket de state do Terraform (`fiap-tc-1-terraform-backend`) foi apagado numa limpeza total — pra recriar a infra AWS do zero, precisa repetir o bootstrap em 2 fases (ver `docs/INFRAESTRUTURA.md`, seção "Como aplicar").

## Estrutura de manifests (Kustomize)

```
k8s/base/            # compartilhado entre local e AWS
k8s/overlays/local/   # cluster Docker Desktop (kind)
k8s/overlays/aws/     # cluster EKS (Auto Mode)
```

`k8s/base/02-secret.yaml` **não é versionado** (gitignored, credenciais reais) — usar `k8s/base/02-secret.yaml.example` como modelo.

## Terraform (`terraform/`)

Cluster EKS com **Auto Mode** habilitado (a AWS gerencia compute/storage/load balancing nativamente — por isso não instalamos EBS CSI driver nem AWS Load Balancer Controller manualmente). Detalhes completos, incluindo o checklist "adaptando pra uma conta AWS diferente" (nomes de bucket, IAM roles, `access_entry_principal_arn`), estão em `docs/INFRAESTRUTURA.md`.

**Gotcha recorrente**: bucket S3 do state tem versionamento — apagar exige remover todas as versões/marcadores antes (`aws s3api list-object-versions` + `delete-objects`), não só `aws s3 rb --force`.

## CI/CD (`.github/workflows/`)

- `app.yml`: `build` (deps+migrations, com MySQL de serviço) → `unit-tests` + `phpstan` (paralelo, sem banco) → `build-and-push` (imagens Docker multi-arch, tag = SHA do commit) → `deploy` (kubectl apply -k no overlay AWS). `phpunit.xml` usa `<env force="false">` pro `DB_HOST` etc., pra funcionar em local (Compose, hostname `database`) e CI (`127.0.0.1`) sem editar nada entre ambientes.
- `infra.yml`: `plan` automático em push/PR; `apply`/`destroy` só via `workflow_dispatch` manual — nunca sozinho.
- Requer Secrets no GitHub: `DOCKERHUB_USERNAME/TOKEN`, `AWS_ACCESS_KEY_ID/SECRET_ACCESS_KEY/SESSION_TOKEN` (temporários, precisam atualizar a cada sessão do Academy Lab), `SECRET_DB_*`/`SECRET_APP_KEY`/etc. (pro Secret da app gerado em runtime pelo pipeline, nunca commitado).

## Lições/gotchas já aprendidos (evitar repetir)

- **Nunca reaproveitar tag de imagem** — sempre usar SHA do commit ou incrementar; reaproveitar tag com `imagePullPolicy: IfNotPresent` deixa nodes servindo versão antiga em cache.
- **Imagens precisam ser multi-arquitetura** (`docker buildx build --platform linux/amd64,linux/arm64`) — Mac local é arm64, EKS é amd64; imagem single-arch dá `exec format error` no ambiente errado.
- **`Job` do Kubernetes é imutável** — trocar imagem exige `kubectl delete job ... && kubectl apply` (ou usar nome único por deploy); por isso o pipeline sempre apaga o Job de migration antes de reaplicar.
- **EKS Auto Mode**: nunca cria instância `nano`/`micro`/`small` nem com menos de 2 vCPU (limite da própria AWS); `StorageClass`/`IngressClass` não são criados automaticamente, precisam ser declarados (`k8s/overlays/aws/`); `bootstrap_self_managed_addons` precisa ser `false` quando Auto Mode está ligado.
- **`metrics-server`** não vem instalado por padrão em nenhum dos dois clusters — precisa aplicar manualmente (HPA fica `<unknown>` sem ele).
- Ambiente de desenvolvimento do dia a dia é **Docker Compose**, não Kubernetes — K8s serve pra validar comportamento de produção (réplicas, HPA, rolling update), não pra iteração rápida de código (imagem precisa rebuild a cada mudança).

## Pendências conhecidas (discutidas, não implementadas)

- RBAC restrito (`Role`/`RoleBinding`) só pra `port-forward` no MySQL, pra acesso de suporte (N2/N3) sem dar acesso amplo ao cluster.
- Trocar a imagem base da API por uma "hardened" (menos CVEs) — pesquisa inicial feita, não aplicada.
- `docs/INFRAESTRUTURA.md` pode precisar de atualização pontual conforme o projeto evolui (ex: passo do `metrics-server` no provisionamento AWS).

## Links

- Repositório: https://github.com/FIAP-TC/fiap-tc-1
- Vídeo explicativo (YouTube): https://www.youtube.com/watch?v=vpj2s93mD2w
