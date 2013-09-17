echo '{
    "provider"    : "email",
    "external_id" : "xx@leaskh.com",
    "password"    : "000000"
}' | /usr/local/share/python/http --verbose post leask.carenodes.com/v1/people/signin
