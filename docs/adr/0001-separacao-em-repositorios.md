# ADR-0001: Separação da infraestrutura em repositórios

**Status:** Aceita
**Data:** 2026-10-12

## Contexto

Até este momento, o fiap-tc-1 concentrava tudo em um único repositório: a aplicação Laravel, o Terraform do cluster EKS e da VPC, e os manifestos Kubernetes de deploy. Isso dificultava o acompanhamento e a organização da infraestrutura, e não atendia ao requisito de repositórios separados com CI/CD independente.

## Decisão

O Terraform do cluster (VPC, subnets, EKS com Auto Mode, bucket de state e o workflow `infra.yml`) foi movido inteiro para o novo repositório `infra-k8s`.

Os manifestos Kubernetes serão divididos por natureza do recurso: os de plataforma (`namespace`, `storageclass`, `ingressclass`, `nodepool` e, futuramente, `metrics-server`) vão para o `infra-k8s`; os da aplicação (Deployment, Service, HPA, Ingress, Job de migration, ConfigMap, Secret) permanecem no fiap-tc-1, que passa a ser o repositório `oficina-api`. Essa parte ainda está em andamento.

## Alternativas consideradas

A divisão em 4 repositórios em si não foi uma escolha nossa: é requisito obrigatório do Tech Challenge. As alternativas reais avaliadas foram sobre como desenhar a fronteira entre eles.

**Reaproveitar o fiap-tc-1 ou criar repositórios do zero.** Optamos por reaproveitar o fiap-tc-1 como oficina-api, preservando o histórico de commits da aplicação e extraindo apenas o terraform/ e parte do k8s/ para repositórios novos. A alternativa de criar os 4 repositórios do zero seria mais simples de montar, mas perderia a rastreabilidade de como a infraestrutura evoluiu.

**Onde ficam os manifestos de k8s/.** Optamos por dividir por natureza do recurso: o que é de plataforma (namespace, storageclass, ingressclass, nodepool) vai para o infra-k8s; o que é da aplicação (Deployment, Service, HPA, Ingress, Job, ConfigMap/Secret) permanece aqui nesse repo. A alternativa de mover o k8s/ inteiro para o infra-k8s centralizaria mais controle, mas faria o pipeline do oficina-api perder o deploy automático, já que o CI da aplicação passaria a apenas construir e publicar a imagem, sem aplicar nada no cluster.

## Consequências

Ficou mais fácil dar manutenção e localizar a documentação de cada parte do projeto, já que cada repositório agora tem uma responsabilidade única. O provisionamento da infraestrutura e a execução dos pipelines também ficaram mais diretos, porque cada repositório passa a ter seu próprio CI/CD dedicado, isolado dos demais.

Por outro lado, a divisão trouxe custo de coordenação: credenciais AWS e outros segredos de CI/CD agora precisam ser configurados separadamente em cada repositório, e mudanças que afetam mais de um deles (por exemplo, renomear o namespace ou alterar uma variável compartilhada) exigem pull requests coordenados em repositórios diferentes, em vez de um único commit atômico. Isso precisa ser monitorado conforme o projeto avança, principalmente perto do prazo de entrega.
