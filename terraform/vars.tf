variable "projectName" {
  default = "fiap-tc-1-terraform-backend"
}

variable "region_default" {
  default = "us-east-1"
}

variable "cidr_vpc" {
  default = "10.0.0.0/16"
}

variable "tags" {
  default = {
    "Name" = "fiap-tc-1-terraform"
  }
}

# variable "access_entry_principal_arn" {
#   default = "arn:aws:sts::398325824630:assumed-role/voclabs/user5297155=marcel.leitefarias@gmail.com"
# }

variable "access_entry_principal_arn" {
  default = "arn:aws:iam::398325824630:role/voclabs"
}

variable "instance_type" {
  default = "t2.micro"
}
