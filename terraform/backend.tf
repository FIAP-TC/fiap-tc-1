terraform {
  backend "s3" {
    bucket = "fiap-tc-1-terraform-backend"
    key    = "fiap-tc-1/terraform.tfstate"
    region = "us-east-1"
  }
}
