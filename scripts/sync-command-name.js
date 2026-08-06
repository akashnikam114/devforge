const fs = require("fs");
const path = require("path");

const projectRoot = path.resolve(__dirname, "..");
const configPath = path.join(projectRoot, "config", "devforge.json");
const packagePath = path.join(projectRoot, "package.json");

function fail(message) {
  console.error(`Unable to sync command name: ${message}`);
  process.exit(1);
}

function readJson(filePath) {
  if (!fs.existsSync(filePath)) {
    fail(`Missing file: ${path.relative(projectRoot, filePath)}`);
  }

  return JSON.parse(fs.readFileSync(filePath, "utf8"));
}

const config = readJson(configPath);
const packageJson = readJson(packagePath);
const commandName = config.commandName;

if (!commandName || !/^[a-zA-Z0-9._-]+$/.test(commandName)) {
  fail("config/devforge.json commandName must contain only letters, numbers, dots, underscores, and hyphens.");
}

packageJson.bin = {
  [commandName]: "./bin/devforge"
};

fs.writeFileSync(packagePath, `${JSON.stringify(packageJson, null, 2)}\n`);

console.log(`Command name synced successfully: ${commandName}`);
console.log("Updated: package.json > bin");

