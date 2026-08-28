resource "aws_eks_cluster" "cluster" {
  name                           = "eks-${var.projectName}"
  bootstrap_self_managed_addons = false

  access_config {
    authentication_mode = "API"
  }

  role_arn = data.aws_iam_role.eks_cluster.arn
  version  = "1.35"

  vpc_config {
    subnet_ids = [
      aws_subnet.subnet_public[0].id,
      aws_subnet.subnet_public[1].id,
      aws_subnet.subnet_public[2].id
    ]
    security_group_ids = [aws_security_group.sg.id]
  }

  compute_config {
    enabled       = true
    node_pools    = ["system"]
    node_role_arn = data.aws_iam_role.eks_node.arn
  }

  kubernetes_network_config {
    elastic_load_balancing {
      enabled = true
    }
  }

  storage_config {
    block_storage {
      enabled = true
    }
  }
}
