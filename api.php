import json
from http.server import BaseHTTPRequestHandler, HTTPServer

class PasswordValidatorHandler(BaseHTTPRequestHandler):
    USERS = {
        "user1": "password1",
        "user2": "password2",
        "user3": "password3"
    }

    def _validate_password(self, username, password):
        if username in self.USERS and self.USERS[username] == password:
            return True
        else:
            return False

    def _parse_parameters(self):
        username = self.path.split("username=")[1].split("&")[0]
        password = self.path.split("password=")[1]
        return (username, password)

    def do_GET(self):
        if self.path.startswith("/validate"):
            username, password = self._parse_parameters()
            is_valid = self._validate_password(username, password)
            self.send_response(200)
            self.send_header("Content-type", "application/json")
            self.end_headers()
            self.wfile.write(json.dumps({"is_valid": is_valid}).encode())

def run(server_class=HTTPServer, handler_class=PasswordValidatorHandler, port=8000):
    server_address = ("", port)
    httpd = server_class(server_address, handler_class)
    print(f"Starting password validator API on port {port}...")
    httpd.serve_forever()

if __name__ == "__main__":
    run()
