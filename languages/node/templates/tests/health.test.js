const assert = require('node:assert');
const test = require('node:test');

const { getHealth } = require('../src/controllers/health.controller');

test('getHealth returns a successful health response', () => {
  let statusCode = null;
  let responseBody = null;

  const response = {
    status(code) {
      statusCode = code;
      return this;
    },
    json(body) {
      responseBody = body;
      return this;
    },
  };

  getHealth({}, response);

  assert.strictEqual(statusCode, 200);
  assert.strictEqual(responseBody.success, true);
  assert.match(responseBody.message, /is running/);
});
