echo '{
    "screen_name" : "Leask",
    "provider"    : "email",
    "external_id" : "i@leaskh.com",
    "password"    : "000000"
}' | /usr/local/share/python/http --verbose post "$carenodes_host/v1/people/signup"
