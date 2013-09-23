echo '{
    "provider"    : "email",
    "external_id" : "xx@leaskh.com",
    "password"    : "000000"
}' | /usr/local/share/python/http --verbose post $carenodes_host/v1/people/signin
