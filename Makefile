metadata-test:
	curl -X "POST" \
		 -H "X-Hasura-Admin-Secret: test" \
		 -H "Content-Type: application/json" \
		 -d "@./tests/Fixture/init-metadata.json" \
		 http://localhost:8080/v1/metadata