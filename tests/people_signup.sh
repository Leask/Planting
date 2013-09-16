echo '{
    "screen_name" : "Leask 2x",
    "provider"    : "email",
    "external_id" : "xx@leaskh.com",
    "password"    : "000000"
}' | /usr/local/share/python/http --verbose post leask.carenodes.com/v1/people/signup
