#!/usr/bin/env python3

import requests
from requests.structures import CaseInsensitiveDict
import json

observability_url = "http://127.0.0.1:8000/api/observability"

data = {
  'targets': {
              "Rigel" : {"ra" : 78.63446707, "dec" : -8.20163836},
              "Procyon" : {"ra" : 114.82549791, "dec" : 5.22498756},
              "Alpha Crucis" : {"ra" : 186.6495634, "dec" : -63.09909286},
              "Antares" : {"ra" : 247.35191542, "dec" : -26.43200261},
            },
  'location': "OT",
  'start_datetime' : "2023-06-10 22:00:00",
  'end_datetime' : "2023-06-11 08:00:00",
  'observability_type' :'any'
}

headers = {"Content-Type": "application/json"}
data_json = json.dumps(data)
response = requests.post(observability_url, headers=headers, data=data_json, verify=True)
response_json = response.json()
#print(response_json)
print(json.dumps(response_json, indent=2))



''' headers = CaseInsensitiveDict()
headers["Accept"] = "application/json"
login_token = login_json['access_token']
headers["Authorization"] = f"Bearer {login_token}"

print(headers)

response = requests.get(proposal_url, headers=headers, verify=True)

print(response.content)

logout_response = requests.post(logout_url, headers=headers, verify=True)

print(logout_response.content) '''

