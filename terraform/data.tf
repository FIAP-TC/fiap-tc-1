# data "aws_iam_user" "example" {
#   user_name = "an_example_user_name"
# }

data "aws_iam_role" "eks_cluster" {
  name = "c220532a5561746l16106281t1w398325-LabEksClusterRole-fBejVMGo9uql"
}

data "aws_iam_role" "eks_node" {
  name = "c220532a5561746l16106281t1w398325824-LabEksNodeRole-pDEbmsYpwQC5"
}
