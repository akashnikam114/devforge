const fs = require("fs");
const path = require("path");
const { spawnSync } = require("child_process");
const readline = require("readline");

const projectRoot = path.resolve(__dirname, "..");
const configPath = path.join(projectRoot, "config", "devforge.json");
const envPath = path.join(projectRoot, ".env");
const useColor = process.stdout.isTTY && process.env.NO_COLOR === undefined;

const color = {
  cyan: (value) => colorize(value, "36"),
  dim: (value) => colorize(value, "2"),
  green: (value) => colorize(value, "32"),
  red: (value) => colorize(value, "31"),
  yellow: (value) => colorize(value, "33"),
  bold: (value) => colorize(value, "1")
};

function colorize(value, code) {
  if (!useColor) {
    return value;
  }

  return `\u001b[${code}m${value}\u001b[0m`;
}

function loadEnvFile() {
  if (!fs.existsSync(envPath)) {
    return;
  }

  const lines = fs.readFileSync(envPath, "utf8").split(/\r?\n/);

  lines.forEach((line) => {
    const trimmedLine = line.trim();

    if (!trimmedLine || trimmedLine.startsWith("#")) {
      return;
    }

    const separatorIndex = trimmedLine.indexOf("=");

    if (separatorIndex === -1) {
      return;
    }

    const key = trimmedLine.slice(0, separatorIndex).trim();
    const value = trimmedLine.slice(separatorIndex + 1).trim();

    if (key && process.env[key] === undefined) {
      process.env[key] = value;
    }
  });
}

function getSupportedTemplates(config) {
  const languagesRoot = path.join(projectRoot, config.languagesDirectory);

  if (!fs.existsSync(languagesRoot)) {
    return [];
  }

  return fs
    .readdirSync(languagesRoot, { withFileTypes: true })
    .filter((entry) => entry.isDirectory() && !entry.name.startsWith("_"))
    .map((entry) => {
      const manifestPath = path.join(languagesRoot, entry.name, "manifest.json");

      if (!fs.existsSync(manifestPath)) {
        return {
          id: entry.name,
          name: entry.name
        };
      }

      const manifest = readJsonFile(manifestPath);

      return {
        id: manifest.id || entry.name,
        name: manifest.name || entry.name
      };
    })
    .sort((firstTemplate, secondTemplate) => firstTemplate.id.localeCompare(secondTemplate.id));
}

function formatSupportedTemplates(config) {
  const supportedTemplates = getSupportedTemplates(config);

  if (supportedTemplates.length === 0) {
    return "No templates are currently available.";
  }

  return supportedTemplates
    .map((template) => `${template.id} (${template.name})`)
    .join(", ");
}

function printUsage(config) {
  const commandName = config.commandName;
  const languageOptionName = config.languageOptionName;
  const firstTemplate = getSupportedTemplates(config)[0];
  const exampleTemplate = firstTemplate ? firstTemplate.id : "node";

  console.log(`${config.appName} CLI`);
  console.log("");
  console.log("Usage:");
  console.log(`  ${commandName} create PROJECT_NAME ${languageOptionName} TEMPLATE`);
  console.log(`  ${commandName} create PROJECT_NAME ${languageOptionName} TEMPLATE --skip-install`);
  console.log(`  ${commandName} feature --list`);
  console.log(`  ${commandName} feature PROJECT_NAME`);
  console.log(`  ${commandName} crud PROJECT_NAME MODULE_NAME`);
  console.log("");
  console.log("Example:");
  console.log(`  ${commandName} create blog-api ${languageOptionName} ${exampleTemplate}`);
  console.log(`  ${commandName} feature blog-api`);
  console.log(`  ${commandName} crud blog-api Category --fields name:string,description:text,is_active:boolean`);
  console.log("");
  console.log("Available templates:");
  console.log(`  ${formatSupportedTemplates(config)}`);
  console.log("");
  console.log("Options:");
  console.log(`  ${languageOptionName} TEMPLATE  Select the coding language or framework template.`);
  console.log("  --skip-install   Create files only and skip package installation.");
  console.log("  --yes            Confirm install/setup prompts for non-interactive runs.");
  console.log("  --feature NAME   Add a Laravel feature directly without the numbered prompt.");
  console.log("  --fields LIST    CRUD fields, for example name:string,description:text,is_active:boolean.");
  console.log("  --force          Allow CRUD files for the same module to be overwritten.");
  console.log("");
  console.log(`Generated projects are created inside ${config.defaultOutputDirectory}/.`);
}

function fail(message) {
  console.error("");
  console.error(color.red("Unable to complete the command"));
  console.error(`${color.bold("Reason:")} ${message}`);
  console.error("");
  process.exit(1);
}

function readConfig() {
  loadEnvFile();

  if (!fs.existsSync(configPath)) {
    fail("Missing config/devforge.json");
  }

  const fileConfig = JSON.parse(fs.readFileSync(configPath, "utf8"));

  return {
    appName: process.env.DEVFORGE_APP_NAME || fileConfig.appName || "DevForge",
    commandName: process.env.DEVFORGE_COMMAND_NAME || fileConfig.commandName || "devforge",
    defaultOutputDirectory: process.env.DEVFORGE_OUTPUT_DIRECTORY || fileConfig.defaultOutputDirectory || "DevMerge",
    languagesDirectory: process.env.DEVFORGE_LANGUAGES_DIRECTORY || fileConfig.languagesDirectory || "languages",
    languageOptionName: process.env.DEVFORGE_LANGUAGE_OPTION || fileConfig.languageOptionName || "--lang"
  };
}

function getOptionValue(args, optionName) {
  const index = args.indexOf(optionName);

  if (index === -1) {
    return null;
  }

  return args[index + 1] || null;
}

function hasOption(args, optionName) {
  return args.includes(optionName);
}

function slugifyProjectName(projectName) {
  return projectName
    .trim()
    .toLowerCase()
    .replace(/[^a-z0-9._-]+/g, "-")
    .replace(/^-+|-+$/g, "");
}

function toWords(value) {
  return value
    .replace(/([a-z0-9])([A-Z])/g, "$1 $2")
    .replace(/[_-]+/g, " ")
    .replace(/[^a-zA-Z0-9 ]+/g, " ")
    .trim()
    .split(/\s+/)
    .filter(Boolean);
}

function toStudlyCase(value) {
  return toWords(value)
    .map((word) => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
    .join("");
}

function toSnakeCase(value) {
  return toWords(value)
    .map((word) => word.toLowerCase())
    .join("_");
}

function toKebabCase(value) {
  return toWords(value)
    .map((word) => word.toLowerCase())
    .join("-");
}

function toTitleCase(value) {
  return toWords(value)
    .map((word) => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
    .join(" ");
}

function pluralizeWord(value) {
  if (value.endsWith("y") && !/[aeiou]y$/i.test(value)) {
    return `${value.slice(0, -1)}ies`;
  }

  if (/(s|x|z|ch|sh)$/i.test(value)) {
    return `${value}es`;
  }

  return `${value}s`;
}

function pluralizeName(value) {
  const words = toWords(value);

  if (words.length === 0) {
    return value;
  }

  words[words.length - 1] = pluralizeWord(words[words.length - 1]);
  return words.join(" ");
}

function readJsonFile(filePath) {
  return JSON.parse(fs.readFileSync(filePath, "utf8"));
}

function runCommand(command, args, options = {}) {
  console.log(`  ${color.dim("Running:")} ${command} ${args.join(" ")}`);

  const result = spawnSync(command, args, {
    cwd: options.cwd || projectRoot,
    stdio: options.stdio || "inherit",
    encoding: options.encoding || "utf8"
  });

  if (result.error) {
    fail(`${options.failureMessage || "Command failed"}: ${result.error.message}`);
  }

  if (result.status !== 0) {
    fail(`${options.failureMessage || "Command failed"} with exit code ${result.status}.`);
  }

  return result;
}

function captureCommand(command, args) {
  return spawnSync(command, args, {
    cwd: projectRoot,
    encoding: "utf8"
  });
}

function writeProgress(step, totalSteps, title, detail) {
  const completed = Math.round((step / totalSteps) * 20);
  const remaining = 20 - completed;
  const percent = Math.round((step / totalSteps) * 100);
  const bar = `${color.green("#".repeat(completed))}${color.dim("-".repeat(remaining))}`;

  console.log(`${color.dim("[")}${bar}${color.dim("]")} ${color.yellow(`${percent}%`)}  ${color.bold(title)}`);

  if (detail) {
    console.log(`  ${color.dim(detail)}`);
  }
}

function replaceTemplateVariables(content, variables) {
  return content
    .replaceAll("__PROJECT_NAME__", variables.projectName)
    .replaceAll("__PROJECT_SLUG__", variables.projectSlug)
    .replaceAll("__PROJECT_SHORT_NAME__", variables.projectShortName || variables.projectName)
    .replaceAll("__PROJECT_DESCRIPTION__", variables.projectDescription || `${variables.projectName} application.`);
}

function isBinaryTemplateFile(filePath) {
  return [".png", ".jpg", ".jpeg", ".gif", ".webp", ".ico", ".pdf", ".zip", ".ttf", ".woff", ".woff2", ".eot"].includes(path.extname(filePath).toLowerCase());
}

function shouldIgnoreTemplatePath(relativePath, ignorePaths) {
  return ignorePaths.some((ignorePath) => {
    return relativePath === ignorePath || relativePath.startsWith(`${ignorePath}/`);
  });
}

function copyTemplateDirectory(sourceDirectory, targetDirectory, variables, options = {}, currentRelativePath = "") {
  if (!fs.existsSync(sourceDirectory)) {
    return;
  }

  ensureDirectory(targetDirectory);

  const ignorePaths = options.ignorePaths || [];

  fs.readdirSync(sourceDirectory, { withFileTypes: true }).forEach((entry) => {
    if (entry.name === ".gitkeep") {
      return;
    }

    const relativePath = currentRelativePath ? path.join(currentRelativePath, entry.name) : entry.name;

    if (shouldIgnoreTemplatePath(relativePath, ignorePaths)) {
      return;
    }

    const sourcePath = path.join(sourceDirectory, entry.name);
    const targetPath = path.join(targetDirectory, entry.name);

    if (entry.isDirectory()) {
      fs.mkdirSync(targetPath, { recursive: true, mode: 0o755 });
      fs.chmodSync(targetPath, 0o755);
      copyTemplateDirectory(sourcePath, targetPath, variables, options, relativePath);
      return;
    }

    if (isBinaryTemplateFile(sourcePath)) {
      fs.copyFileSync(sourcePath, targetPath);
      fs.chmodSync(targetPath, 0o644);
      return;
    }

    const content = fs.readFileSync(sourcePath, "utf8");
    const compiledContent = replaceTemplateVariables(content, variables);

    fs.writeFileSync(targetPath, compiledContent, { mode: 0o644 });
    fs.chmodSync(targetPath, 0o644);
  });
}

function ensureDirectory(directoryPath) {
  fs.mkdirSync(directoryPath, { recursive: true, mode: 0o755 });
  fs.chmodSync(directoryPath, 0o755);
}

function writeProjectFile(filePath, content) {
  ensureDirectory(path.dirname(filePath));
  fs.writeFileSync(filePath, content, { mode: 0o644 });
  fs.chmodSync(filePath, 0o644);
}

function installDependencies(projectDirectory, manifest) {
  const installConfig = manifest.install;

  if (!installConfig || !installConfig.enabled) {
    return;
  }

  const result = spawnSync(installConfig.command, installConfig.args || [], {
    cwd: projectDirectory,
    stdio: "inherit"
  });

  if (result.error) {
    fail(`Dependency installation failed: ${result.error.message}`);
  }

  if (result.status !== 0) {
    fail(`Dependency installation failed with exit code ${result.status}.`);
  }
}

function compareVersions(currentVersion, minimumVersion) {
  const currentParts = currentVersion.split(".").map((part) => Number(part));
  const minimumParts = minimumVersion.split(".").map((part) => Number(part));
  const maxLength = Math.max(currentParts.length, minimumParts.length);

  for (let index = 0; index < maxLength; index += 1) {
    const currentPart = currentParts[index] || 0;
    const minimumPart = minimumParts[index] || 0;

    if (currentPart > minimumPart) {
      return 1;
    }

    if (currentPart < minimumPart) {
      return -1;
    }
  }

  return 0;
}

function checkLaravelRequirements(generator) {
  const phpResult = captureCommand("php", ["-r", "echo PHP_VERSION;"]);

  if (phpResult.error || phpResult.status !== 0) {
    fail("PHP is required for Laravel generation, but the php command is not available.");
  }

  const phpVersion = phpResult.stdout.trim();

  if (compareVersions(phpVersion, generator.minimumPhpVersion) < 0) {
    fail(`Laravel ${generator.laravelVersion} requires PHP ${generator.minimumPhpVersion} or higher. Current PHP version is ${phpVersion}.`);
  }

  const composerResult = captureCommand("composer", ["--version"]);

  if (composerResult.error || composerResult.status !== 0) {
    fail("Composer is required for Laravel generation, but the composer command is not available.");
  }

  return {
    phpVersion,
    composerVersion: composerResult.stdout.trim()
  };
}

function createPrompt() {
  return readline.createInterface({
    input: process.stdin,
    output: process.stdout
  });
}

function askQuestion(prompt, question, defaultValue = "") {
  return new Promise((resolve) => {
    const suffix = defaultValue ? ` ${color.dim(`default: ${defaultValue}`)}` : "";

    prompt.question(`${color.cyan(question)}${suffix}\n${color.green("›")} `, (answer) => {
      resolve(answer.trim() || defaultValue);
    });
  });
}

function splitPromptMessage(question) {
  const questionMarkIndex = question.indexOf("?");

  if (questionMarkIndex === -1 || questionMarkIndex === question.length - 1) {
    return {
      title: question,
      description: ""
    };
  }

  return {
    title: question.slice(0, questionMarkIndex + 1),
    description: question.slice(questionMarkIndex + 1).trim()
  };
}

async function askChoice(prompt, question, choices, defaultValue) {
  while (true) {
    console.log("");
    console.log(color.bold(question));
    choices.forEach((choice, index) => {
      const defaultMarker = choice === defaultValue ? color.dim(" default") : "";
      console.log(`  ${color.cyan(`${index + 1}.`)} ${choice}${defaultMarker}`);
    });
    console.log("");

    const answer = await askQuestion(prompt, "Enter option number or value", defaultValue);
    const selectedByIndex = choices[Number(answer) - 1];
    const selected = selectedByIndex || answer;

    if (choices.includes(selected)) {
      return selected;
    }

    console.log("");
    console.log(color.yellow(`Invalid selection "${answer}". Please choose: ${choices.join(", ")}.`));
    console.log("");
  }
}

async function askYesNo(prompt, question, defaultValue = "no") {
  const normalizedDefault = defaultValue.toLowerCase() === "yes" ? "yes" : "no";
  const message = splitPromptMessage(question);

  while (true) {
    console.log("");
    console.log(color.bold(message.title));

    if (message.description) {
      console.log(color.dim(message.description));
    }

    const answer = await askQuestion(prompt, "Choose yes or no", normalizedDefault);
    const normalizedAnswer = answer.toLowerCase();

    if (["yes", "y"].includes(normalizedAnswer)) {
      return true;
    }

    if (["no", "n"].includes(normalizedAnswer)) {
      return false;
    }

    console.log("");
    console.log(color.yellow("Please answer yes or no."));
    console.log("");
  }
}

async function askNumberChoice(prompt, question, choices) {
  while (true) {
    const answer = await askQuestion(prompt, question);
    const selectedIndex = Number(answer) - 1;

    if (Number.isInteger(selectedIndex) && choices[selectedIndex]) {
      return choices[selectedIndex];
    }

    console.log("");
    console.log(color.yellow(`Invalid selection "${answer}". Please enter a number from 1 to ${choices.length}.`));
    console.log("");
  }
}

function isHexColor(value) {
  return /^#[0-9a-fA-F]{6}$/.test(value);
}

async function askHexColor(prompt, question, defaultValue) {
  while (true) {
    const answer = await askQuestion(prompt, question, defaultValue);

    if (isHexColor(answer)) {
      return answer;
    }

    console.log("");
    console.log(color.yellow(`Invalid color "${answer}". Please enter a 6-digit hex color like #049C9C.`));
    console.log("");
  }
}

async function collectLaravelTimezone(args) {
  const supportedTimezones = ["Asia/Kolkata", "UTC"];
  const defaultTimezone = getOptionValue(args, "--timezone") || "Asia/Kolkata";

  if (!supportedTimezones.includes(defaultTimezone)) {
    fail(`Unsupported timezone "${defaultTimezone}". Supported timezones: ${supportedTimezones.join(", ")}.`);
  }

  if (!process.stdin.isTTY) {
    console.log("");
    console.log("Laravel timezone selection");
    console.log(`No interactive terminal was detected, so DevForge is using ${defaultTimezone}.`);
    return defaultTimezone;
  }

  const prompt = createPrompt();

  try {
    console.log("");
    console.log("Laravel timezone selection");
    console.log("Choose the timezone for config/app.php.");
    console.log("");

    return await askChoice(prompt, "Select Timezone", supportedTimezones, defaultTimezone);
  } finally {
    prompt.close();
  }
}

function getSupportedLaravelConnections() {
  return ["mysql", "pgsql", "sqlite", "sqlsrv"];
}

function getDefaultLaravelPort(dbConnection) {
  const defaultPorts = {
    mysql: "3306",
    pgsql: "5432",
    sqlsrv: "1433"
  };

  return defaultPorts[dbConnection] || "";
}

function assertValidLaravelConnection(dbConnection) {
  const supportedConnections = getSupportedLaravelConnections();

  if (!supportedConnections.includes(dbConnection)) {
    fail(`Unsupported Laravel database connection "${dbConnection}". Supported connections: ${supportedConnections.join(", ")}.`);
  }
}

async function askLaravelConnection(prompt, defaultValue) {
  const supportedConnections = getSupportedLaravelConnections();

  while (true) {
    const dbConnection = await askQuestion(
      prompt,
      `Enter DB Connection [${supportedConnections.join("/")}]`,
      defaultValue
    );

    if (supportedConnections.includes(dbConnection)) {
      return dbConnection;
    }

    console.log("");
    console.log(`Invalid DB connection "${dbConnection}". Please choose one of: ${supportedConnections.join(", ")}.`);
    console.log("");
  }
}

function getLaravelDatabaseDefaults(projectName, args) {
  const dbConnection = getOptionValue(args, "--db-connection") || "mysql";
  const dbDatabase = getOptionValue(args, "--db-name") || (
    dbConnection === "sqlite"
      ? "database/database.sqlite"
      : slugifyProjectName(projectName).replaceAll("-", "_")
  );

  return {
    DB_CONNECTION: dbConnection,
    DB_HOST: getOptionValue(args, "--db-host") || (dbConnection === "sqlite" ? "" : "127.0.0.1"),
    DB_PORT: getOptionValue(args, "--db-port") || getDefaultLaravelPort(dbConnection),
    DB_DATABASE: dbDatabase,
    DB_USERNAME: getOptionValue(args, "--db-user") || (dbConnection === "sqlite" ? "" : "root"),
    DB_PASSWORD: getOptionValue(args, "--db-password") || ""
  };
}

async function collectLaravelDatabaseConfig(projectName, args) {
  const defaults = getLaravelDatabaseDefaults(projectName, args);
  assertValidLaravelConnection(defaults.DB_CONNECTION);

  if (!process.stdin.isTTY) {
    console.log("");
    console.log("Laravel database configuration");
    console.log("No interactive terminal was detected, so DevForge is using database values from CLI options and defaults.");
    return defaults;
  }

  const prompt = createPrompt();

  try {
    console.log("");
    console.log("Laravel database configuration");
    console.log("Press Enter to accept the suggested value.");
    console.log("");

    const dbConnection = await askLaravelConnection(prompt, defaults.DB_CONNECTION);

    if (dbConnection === "sqlite") {
      const sqliteDatabase = await askQuestion(prompt, "Enter SQLite database path", defaults.DB_DATABASE);

      return {
        DB_CONNECTION: dbConnection,
        DB_HOST: "",
        DB_PORT: "",
        DB_DATABASE: sqliteDatabase,
        DB_USERNAME: "",
        DB_PASSWORD: ""
      };
    }

    const dbHost = await askQuestion(prompt, "Enter DB Host", defaults.DB_HOST);
    const dbPort = await askQuestion(prompt, "Enter DB Port", getDefaultLaravelPort(dbConnection));
    const dbDatabase = await askQuestion(prompt, "Enter DB Database Name", defaults.DB_DATABASE);
    const dbUsername = await askQuestion(prompt, "Enter DB Username", defaults.DB_USERNAME);
    const dbPassword = await askQuestion(prompt, "Enter DB Password", defaults.DB_PASSWORD);

    return {
      DB_CONNECTION: dbConnection,
      DB_HOST: dbHost,
      DB_PORT: dbPort,
      DB_DATABASE: dbDatabase,
      DB_USERNAME: dbUsername,
      DB_PASSWORD: dbPassword
    };
  } finally {
    prompt.close();
  }
}

function validateLaravelDatabaseConnection(databaseConfig) {
  assertValidLaravelConnection(databaseConfig.DB_CONNECTION);

  const phpScript = `
    $connection = getenv('DEVFORGE_DB_CONNECTION');
    $host = getenv('DEVFORGE_DB_HOST');
    $port = getenv('DEVFORGE_DB_PORT');
    $database = getenv('DEVFORGE_DB_DATABASE');
    $username = getenv('DEVFORGE_DB_USERNAME');
    $password = getenv('DEVFORGE_DB_PASSWORD');

    try {
        if ($connection === 'sqlite') {
            if ($database !== ':memory:' && !file_exists($database)) {
                fwrite(STDERR, "SQLite database file does not exist: {$database}");
                exit(2);
            }

            new PDO("sqlite:{$database}");
        } elseif ($connection === 'mysql') {
            new PDO("mysql:host={$host};port={$port};dbname={$database}", $username, $password, [
                PDO::ATTR_TIMEOUT => 5,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
        } elseif ($connection === 'pgsql') {
            new PDO("pgsql:host={$host};port={$port};dbname={$database}", $username, $password, [
                PDO::ATTR_TIMEOUT => 5,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
        } elseif ($connection === 'sqlsrv') {
            new PDO("sqlsrv:Server={$host},{$port};Database={$database}", $username, $password, [
                PDO::ATTR_TIMEOUT => 5,
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            ]);
        }
    } catch (Throwable $error) {
        fwrite(STDERR, $error->getMessage());
        exit(1);
    }
  `;

  const result = spawnSync("php", ["-r", phpScript], {
    cwd: projectRoot,
    encoding: "utf8",
    env: {
      ...process.env,
      DEVFORGE_DB_CONNECTION: databaseConfig.DB_CONNECTION,
      DEVFORGE_DB_HOST: databaseConfig.DB_HOST,
      DEVFORGE_DB_PORT: databaseConfig.DB_PORT,
      DEVFORGE_DB_DATABASE: databaseConfig.DB_DATABASE,
      DEVFORGE_DB_USERNAME: databaseConfig.DB_USERNAME,
      DEVFORGE_DB_PASSWORD: databaseConfig.DB_PASSWORD
    }
  });

  return {
    valid: result.status === 0,
    message: result.stderr.trim() || result.stdout.trim()
  };
}

async function collectAndValidateLaravelDatabaseConfig(projectName, args) {
  while (true) {
    const databaseConfig = await collectLaravelDatabaseConfig(projectName, args);
    const validation = validateLaravelDatabaseConnection(databaseConfig);

    if (validation.valid) {
      console.log("");
      console.log("Database connection verified successfully.");
      return databaseConfig;
    }

    console.log("");
    console.log("Database connection failed.");
    console.log(`Reason: ${validation.message || "Unable to connect with the provided database settings."}`);

    if (!process.stdin.isTTY) {
      fail("Laravel database validation failed. Check the database options and run the command again.");
    }

    const prompt = createPrompt();

    try {
      const retry = await askQuestion(prompt, "Do you want to enter database details again? Type yes to retry", "yes");

      if (retry.toLowerCase() !== "yes") {
        fail("Laravel database validation cancelled.");
      }
    } finally {
      prompt.close();
    }
  }
}

async function askForSensitiveConfirmation(config, manifest, autoConfirm) {
  if (autoConfirm) {
    console.log("");
    console.log("Sensitive work confirmation");
    console.log("Auto-confirmed with --yes.");
    return true;
  }

  if (!process.stdin.isTTY) {
    fail("Composer installation requires confirmation. Run the command interactively or pass --yes.");
  }

  const prompt = createPrompt();

  try {
    console.log("");
    console.log("Sensitive work confirmation");
    console.log(`This will run Composer commands to create Laravel ${manifest.generator.laravelVersion}, install packages, publish config files, generate JWT secret, and create a storage link.`);
    if (manifest.generator.securityNotice) {
      console.log("");
      console.log("Security notice:");
      console.log(manifest.generator.securityNotice);
    }
    console.log("");

    const answer = await askQuestion(prompt, "Continue? Type yes to proceed", "no");
    return answer.toLowerCase() === "yes";
  } finally {
    prompt.close();
  }
}

function getLaravelFeatureRegistry() {
  return readLaravelFeatureManifest().features;
}

function getLaravelFeaturesRoot() {
  return path.join(projectRoot, "languages", "laravel", "features");
}

function readLaravelFeatureManifest() {
  const manifestPath = path.join(getLaravelFeaturesRoot(), "manifest.json");

  if (!fs.existsSync(manifestPath)) {
    fail(`Missing Laravel feature manifest at ${path.relative(projectRoot, manifestPath)}.`);
  }

  return readJsonFile(manifestPath);
}

function getLaravelFeatureDefinition(featureId) {
  const feature = getLaravelFeatureRegistry().find((item) => item.id === featureId);

  if (!feature) {
    fail(`Unknown Laravel feature "${featureId}". Run devforge feature --list to see available features.`);
  }

  return feature;
}

function compileLaravelFeatureContent(content, projectName) {
  return replaceTemplateVariables(content, {
    projectName,
    projectSlug: slugifyProjectName(projectName),
    projectShortName: getProjectShortName(projectName),
    projectDescription: `${projectName} application.`
  });
}

function readLaravelFeatureFile(sourcePath, projectName) {
  const fullSourcePath = path.join(getLaravelFeaturesRoot(), sourcePath);

  if (!fs.existsSync(fullSourcePath)) {
    fail(`Missing Laravel feature file at ${path.relative(projectRoot, fullSourcePath)}.`);
  }

  if (isBinaryTemplateFile(fullSourcePath)) {
    return fs.readFileSync(fullSourcePath);
  }

  return compileLaravelFeatureContent(fs.readFileSync(fullSourcePath, "utf8"), projectName);
}

const crudFieldTypes = {
  string: {
    migration: (name) => `$table->string('${name}');`,
    validation: "required|string|max:255",
    input: "text"
  },
  text: {
    migration: (name) => `$table->text('${name}')->nullable();`,
    validation: "nullable|string",
    input: "textarea"
  },
  integer: {
    migration: (name) => `$table->integer('${name}')->default(0);`,
    validation: "required|integer",
    input: "number"
  },
  decimal: {
    migration: (name) => `$table->decimal('${name}', 10, 2)->default(0);`,
    validation: "required|numeric",
    input: "number"
  },
  boolean: {
    migration: (name) => `$table->boolean('${name}')->default(false);`,
    validation: "nullable|boolean",
    input: "select",
    cast: "boolean"
  },
  date: {
    migration: (name) => `$table->date('${name}')->nullable();`,
    validation: "nullable|date",
    input: "date"
  },
  datetime: {
    migration: (name) => `$table->dateTime('${name}')->nullable();`,
    validation: "nullable|date",
    input: "datetime-local"
  },
  email: {
    migration: (name) => `$table->string('${name}');`,
    validation: "required|email|max:255",
    input: "email"
  },
  url: {
    migration: (name) => `$table->string('${name}')->nullable();`,
    validation: "nullable|url|max:255",
    input: "url"
  }
};

function parseCrudFields(fieldInput) {
  const input = (fieldInput || "name:string,description:text,is_active:boolean").trim();
  const fields = input
    .split(",")
    .map((item) => item.trim())
    .filter(Boolean)
    .map((item) => {
      const [rawName, rawType = "string"] = item.split(":").map((part) => part.trim());
      const name = toSnakeCase(rawName);
      const type = rawType.toLowerCase();

      if (!name || !/^[a-z][a-z0-9_]*$/.test(name)) {
        fail(`Invalid CRUD field "${rawName}". Use names like name, title, support_email, or is_active.`);
      }

      if (!crudFieldTypes[type]) {
        fail(`Invalid CRUD field type "${type}" for "${name}". Supported types: ${Object.keys(crudFieldTypes).join(", ")}.`);
      }

      return {
        name,
        type,
        label: toTitleCase(name),
        config: crudFieldTypes[type]
      };
    });

  const uniqueNames = new Set(fields.map((field) => field.name));

  if (fields.length === 0) {
    fail("CRUD requires at least one field.");
  }

  if (uniqueNames.size !== fields.length) {
    fail("CRUD fields must be unique.");
  }

  return fields;
}

function buildLaravelCrudDefinition(moduleName, fieldInput) {
  const modelClass = toStudlyCase(moduleName);

  if (!modelClass || !/^[A-Z][A-Za-z0-9]*$/.test(modelClass)) {
    fail("CRUD module name must contain letters or numbers, for example Category, BlogPost, or Product Type.");
  }

  const singularTitle = toTitleCase(modelClass);
  const pluralTitle = pluralizeName(singularTitle);
  const routeName = toSnakeCase(pluralTitle);
  const fields = parseCrudFields(fieldInput);

  return {
    moduleName,
    modelClass,
    controllerClass: `${modelClass}Controller`,
    serviceClass: `${modelClass}Service`,
    migrationClass: `Create${toStudlyCase(routeName)}Table`,
    tableName: routeName,
    routeName,
    viewDirectory: routeName,
    singularTitle,
    pluralTitle,
    lowerSingular: singularTitle.toLowerCase(),
    lowerPlural: pluralTitle.toLowerCase(),
    fields,
    hasStatus: fields.some((field) => field.name === "is_active" && field.type === "boolean")
  };
}

function renderCrudStub(stubRelativePath, definition, extraVariables = {}) {
  const stubPath = path.join(getLaravelFeaturesRoot(), "crud", "stubs", stubRelativePath);

  if (!fs.existsSync(stubPath)) {
    fail(`Missing Laravel CRUD stub at ${path.relative(projectRoot, stubPath)}.`);
  }

  const variables = {
    MODEL_CLASS: definition.modelClass,
    CONTROLLER_CLASS: definition.controllerClass,
    SERVICE_CLASS: definition.serviceClass,
    MIGRATION_CLASS: definition.migrationClass,
    TABLE_NAME: definition.tableName,
    ROUTE_NAME: definition.routeName,
    VIEW_DIRECTORY: definition.viewDirectory,
    TITLE_SINGULAR: definition.singularTitle,
    TITLE_PLURAL: definition.pluralTitle,
    LOWER_SINGULAR: definition.lowerSingular,
    LOWER_PLURAL: definition.lowerPlural,
    ...extraVariables
  };

  return Object.entries(variables).reduce((content, [key, value]) => {
    return content.replaceAll(`__${key}__`, value);
  }, fs.readFileSync(stubPath, "utf8"));
}

function renderCrudFillableFields(fields) {
  return fields.map((field) => `        '${field.name}',`).join("\n");
}

function renderCrudCastsBlock(fields) {
  const casts = fields.filter((field) => field.config.cast);

  if (casts.length === 0) {
    return "";
  }

  return `    protected $casts = [\n${casts.map((field) => `        '${field.name}' => '${field.config.cast}',`).join("\n")}\n    ];`;
}

function renderCrudMigrationFields(fields) {
  return fields.map((field) => `            ${field.config.migration(field.name)}`).join("\n");
}

function renderCrudValidationRules(fields) {
  return fields.map((field) => `            '${field.name}' => '${field.config.validation}',`).join("\n");
}

function renderCrudDataTableColumns(fields) {
  return fields
    .filter((field) => field.type === "boolean")
    .map((field) => {
      const statusClick = field.name === "is_active"
        ? "                    $nextStatus = $rec->is_active ? 0 : 1;\n                    return '<div><span class=\"tb-status ' . $class . '\" style=\"cursor:pointer\" onclick=\"changeStatus(' . $rec->id . ',' . $nextStatus . ')\">' . $label . '</span></div>';"
        : "                    return '<div><span class=\"tb-status ' . $class . '\">' . $label . '</span></div>';";

      return `                ->addColumn('${field.name}', function ($rec) {\n                    $label = $rec->${field.name} ? 'Active' : 'Inactive';\n                    $class = $rec->${field.name} ? 'text-success' : 'text-danger';\n${statusClick}\n                })`;
    })
    .join("\n");
}

function renderCrudRawColumns(definition) {
  const rawColumns = ["'action'"];

  definition.fields
    .filter((field) => field.type === "boolean")
    .forEach((field) => rawColumns.unshift(`'${field.name}'`));

  return rawColumns.join(", ");
}

function renderCrudTableHeaders(fields) {
  return fields.map((field) => `                                <th>${field.label}</th>`).join("\n");
}

function renderCrudTableColumns(fields) {
  return fields.map((field) => `                    {\n                        "mData": "${field.name}"\n                    },`).join("\n");
}

function renderCrudFormFields(fields) {
  return fields.map((field) => {
    const oldValue = `old('${field.name}', $data->${field.name} ?? '')`;

    if (field.config.input === "textarea") {
      return `                            <div class="col-lg-12">\n                                <div class="form-group">\n                                    <label class="form-label" for="${field.name}">${field.label}</label>\n                                    <div class="form-control-wrap">\n                                        <textarea class="form-control form-control-lg @error('${field.name}') is-invalid @enderror" name="${field.name}" id="${field.name}" placeholder="${field.label}">{{ ${oldValue} }}</textarea>\n                                        @error('${field.name}')\n                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>\n                                        @enderror\n                                    </div>\n                                </div>\n                            </div>`;
    }

    if (field.config.input === "select") {
      return `                            <div class="col-lg-12">\n                                <div class="form-group">\n                                    <label class="form-label" for="${field.name}">${field.label}</label>\n                                    <div class="form-control-wrap">\n                                        <select class="form-control form-control-lg @error('${field.name}') is-invalid @enderror" name="${field.name}" id="${field.name}">\n                                            <option value="1" {{ (string) ${oldValue} === '1' ? 'selected' : '' }}>Active</option>\n                                            <option value="0" {{ (string) ${oldValue} === '0' ? 'selected' : '' }}>Inactive</option>\n                                        </select>\n                                        @error('${field.name}')\n                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>\n                                        @enderror\n                                    </div>\n                                </div>\n                            </div>`;
    }

    return `                            <div class="col-lg-12">\n                                <div class="form-group">\n                                    <label class="form-label" for="${field.name}">${field.label}</label>\n                                    <div class="form-control-wrap">\n                                        <input type="${field.config.input}" class="form-control form-control-lg @error('${field.name}') is-invalid @enderror" name="${field.name}" id="${field.name}" value="{{ ${oldValue} }}" placeholder="${field.label}">\n                                        @error('${field.name}')\n                                            <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>\n                                        @enderror\n                                    </div>\n                                </div>\n                            </div>`;
  }).join("\n\n");
}

function renderCrudStatusPieces(definition) {
  if (!definition.hasStatus) {
    return {
      route: "",
      method: "",
      script: ""
    };
  }

  return {
    route: `            \\Illuminate\\Support\\Facades\\Route::post('change-status', [\\App\\Http\\Controllers\\Admin\\${definition.controllerClass}::class, 'changeStatus']);\n`,
    method: `    public function changeStatus(Request $request)\n    {\n        $response = ${definition.modelClass}::where('id', $request->id)->update(['is_active' => $request->status]);\n\n        if ($response) {\n            $message = $request->status == 1 ? 'Activated' : 'Inactivated';\n            return response()->json(['status' => 'success', 'message' => \"${definition.singularTitle} $message successfully.\"]);\n        }\n\n        return response()->json(['status' => 'error', 'message' => 'Invalid Data']);\n    }`,
    script: `        function changeStatus(id, status) {\n            if ($.trim(id)) {\n                let statusText = status == 1 ? "activate" : "deactivate";\n                Swal.fire({\n                    title: 'Update Status?',\n                    text: "Do you want to " + statusText + " this?",\n                    icon: 'info',\n                    showCancelButton: true,\n                    confirmButtonText: 'Yes, change it!'\n                }).then(function(result) {\n                    if (result.value) {\n                        $.ajax({\n                            headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" },\n                            url: "{{ url('admin/__ROUTE_NAME__/change-status') }}",\n                            type: "POST",\n                            dataType: "JSON",\n                            data: { id: id, status: status },\n                            success: function(response) {\n                                if (response.status == 'success') {\n                                    Swal.fire("Updated", response.message, "success");\n                                    $("#myTable").DataTable().ajax.reload(null, false);\n                                } else {\n                                    Swal.fire("Error!", response.message, "error");\n                                }\n                            },\n                            error: function(xhr, ajaxOptions, thrownError) {\n                                Swal.fire("Error!", "Something went wrong", "error");\n                            }\n                        });\n                    }\n                });\n            }\n        }\n\n`
  };
}

function printLaravelFeatureList() {
  console.log("");
  console.log("Laravel feature list");
  console.log("Choose these features during project creation, or add skipped features later with the feature command.");
  console.log("");

  getLaravelFeatureRegistry().forEach((feature, index) => {
    const alwaysLabel = feature.always ? " (always included)" : "";
    console.log(`${index + 1}] ${feature.title}${alwaysLabel}`);
    console.log(`   Group: ${feature.group}`);
    console.log(`   ${feature.description}`);
  });
}

async function collectLaravelUiThemeConfig(prompt, projectName) {
  console.log("");
  console.log(color.bold("Admin UI Theme Settings"));
  console.log(color.dim("These values make each generated admin panel feel different while keeping the same reusable layout."));

  return {
    DEVFORGE_UI_APP_NAME: `"${await askQuestion(prompt, "Enter admin panel app name", projectName)}"`,
    DEVFORGE_UI_PRIMARY_COLOR: await askHexColor(prompt, "Enter primary color code", "#049C9C"),
    DEVFORGE_UI_SECONDARY_COLOR: await askHexColor(prompt, "Enter secondary color code", "#037a7a"),
    DEVFORGE_UI_PANEL_TITLE: `"${await askQuestion(prompt, "Enter admin panel title", "Admin Panel")}"`,
    DEVFORGE_UI_PANEL_DESCRIPTION: `"${await askQuestion(prompt, "Enter admin panel description", "Manage users, settings, reports, and application operations from one place.")}"`
  };
}

async function collectLaravelStarterFeatureChoices(projectName) {
  if (!process.stdin.isTTY) {
    console.log("");
    console.log("Laravel starter feature selection");
    console.log("No interactive terminal was detected, so DevForge is applying the standard Laravel project structure and always-included RBAC module.");

    return readLaravelFeatureManifest().createDefaults;
  }

  const prompt = createPrompt();

  try {
    console.log("");
    console.log("Laravel starter feature selection");
    console.log("DevForge groups features so you only answer each setup area once. Database is already configured and RBAC is always included.");
    console.log("");

    const projectStructure = await askYesNo(prompt, "Add Project Structure feature? This creates Helpers, Services, Traits, Constants, Jobs, Mails, Notifications, Listeners, asset folders, errors, pages, admin folders, and API folders", "yes");
    const api = await askYesNo(prompt, "Add API Feature? This creates API v1 routes, a BaseApiController, and route provider mapping for API projects", "yes");
    const commonCore = await askYesNo(prompt, "Add Common Core Modules Feature? This creates the full reusable admin starter with auth, dashboard, layout assets, business settings, general settings, restriction settings, banners, push notifications, Firebase service, helpers, migrations, and routes", "yes");
    const adminPanel = commonCore
      ? false
      : await askYesNo(prompt, "Add Admin Panel Feature? This creates a starter dashboard, layout files, and admin route foundation", "no");
    const adminAuth = adminPanel
      ? await askYesNo(prompt, "Add Admin Auth Feature? This creates the admin login flow, logout route, and change-password starter", "yes")
      : false;
    const adminAssets = !commonCore && adminPanel
      ? await askYesNo(prompt, "Add Admin Assets Feature? This copies reusable admin CSS, JavaScript, fonts, and images for the admin layout", "yes")
      : false;
    const adminUiTheme = commonCore || adminPanel
      ? await askYesNo(prompt, "Add Admin UI Theme Feature? This creates dynamic brand colors and app panel settings for a different look per project", "yes")
      : false;
    const themeConfig = adminUiTheme
      ? await collectLaravelUiThemeConfig(prompt, projectName)
      : {};
    const middleware = commonCore
      ? false
      : await askYesNo(prompt, "Add Middleware Feature? This creates secure headers, API logging, sanitization, and maintenance middleware starters", "yes");
    const firebase = commonCore
      ? false
      : await askYesNo(prompt, "Add Firebase Feature? This prepares Firebase service/config placeholders for notification or auth integrations", "no");
    const excelExport = await askYesNo(prompt, "Add Excel Export Feature? This creates SampleExport for maatwebsite/excel", "yes");
    const pdfExport = await askYesNo(prompt, "Add PDF Export Feature? This prepares a PDF service placeholder for DomPDF reports", "no");
    const pwa = await askYesNo(prompt, "Add PWA Feature? This creates manifest, app icon, and service worker cache files using the project name", "yes");

    return {
      projectStructure,
      api,
      adminPanel,
      adminAuth,
      adminUiTheme,
      adminAssets,
      commonCore,
      themeConfig,
      middleware,
      firebase,
      excelExport,
      pdfExport,
      pwa
    };
  } finally {
    prompt.close();
  }
}

function updateLaravelComposerAutoloadFiles(projectDirectory, helperFiles) {
  if (helperFiles.length === 0) {
    return false;
  }

  const composerPath = path.join(projectDirectory, "composer.json");

  if (!fs.existsSync(composerPath)) {
    return false;
  }

  const composerConfig = readJsonFile(composerPath);
  composerConfig.autoload = composerConfig.autoload || {};
  composerConfig.autoload.files = composerConfig.autoload.files || [];

  helperFiles.forEach((helperFile) => {
    if (!composerConfig.autoload.files.includes(helperFile)) {
      composerConfig.autoload.files.push(helperFile);
    }
  });

  fs.writeFileSync(composerPath, `${JSON.stringify(composerConfig, null, 4)}\n`);
  fs.chmodSync(composerPath, 0o644);
  return true;
}

function getProjectShortName(projectName) {
  const words = projectName
    .replace(/([a-z0-9])([A-Z])/g, "$1 $2")
    .replace(/[^a-zA-Z0-9]+/g, " ")
    .trim()
    .split(/\s+/)
    .filter(Boolean);

  if (words.length === 0) {
    return projectName.slice(0, 8);
  }

  return words.map((word) => word[0].toUpperCase()).join("").slice(0, 8);
}

function copyDirectoryContents(sourceDirectory, targetDirectory, variables = {}) {
  ensureDirectory(targetDirectory);
  copyTemplateDirectory(sourceDirectory, targetDirectory, {
    projectName: variables.projectName || "",
    projectSlug: variables.projectSlug || "",
    projectShortName: variables.projectShortName || "",
    projectDescription: variables.projectDescription || ""
  });
}

function copyLaravelExceptionHandler(projectDirectory) {
  const handlerSource = path.join(projectRoot, "languages", "laravel", "templates", "app", "Exceptions", "Handler.php");
  const handlerTarget = path.join(projectDirectory, "app", "Exceptions", "Handler.php");

  if (!fs.existsSync(handlerSource)) {
    return false;
  }

  writeProjectFile(handlerTarget, fs.readFileSync(handlerSource, "utf8"));
  return true;
}

function createLaravelBaseStructure(projectDirectory) {
  readLaravelFeatureManifest().baseDirectories.forEach((directory) => {
    ensureDirectory(path.join(projectDirectory, directory));
    writeProjectFile(path.join(projectDirectory, directory, ".gitkeep"), "");
  });

  const apiRoutePath = path.join(projectDirectory, "routes", "api", "v1", "api.php");
  writeProjectFile(apiRoutePath, readLaravelFeatureFile("api/routes/api/v1/api.php", "Laravel"));

  const defaultApiRoutePath = path.join(projectDirectory, "routes", "api.php");

  if (fs.existsSync(defaultApiRoutePath)) {
    fs.unlinkSync(defaultApiRoutePath);
  }
}

function appendUniqueFileContent(filePath, content, marker) {
  const currentContent = fs.existsSync(filePath) ? fs.readFileSync(filePath, "utf8") : "<?php\n";
  const normalizedContent = content.trim().replace(/^<\?php\s*/, "");

  if (currentContent.includes(marker)) {
    return false;
  }

  const separator = currentContent.endsWith("\n") ? "\n" : "\n\n";
  writeProjectFile(filePath, `${currentContent}${separator}${normalizedContent}\n`);
  return true;
}

function copyLaravelFeatureFiles(projectDirectory, projectName, feature) {
  const createdItems = [];

  (feature.directories || []).forEach((directory) => {
    ensureDirectory(path.join(projectDirectory, directory));
    writeProjectFile(path.join(projectDirectory, directory, ".gitkeep"), "");
    createdItems.push(`${directory}/`);
  });

  (feature.files || []).forEach((file) => {
    const targetPath = path.join(projectDirectory, file.target);
    const sourcePath = path.join(getLaravelFeaturesRoot(), file.source);

    if (isBinaryTemplateFile(sourcePath)) {
      ensureDirectory(path.dirname(targetPath));
      fs.copyFileSync(sourcePath, targetPath);
      fs.chmodSync(targetPath, 0o644);
    } else {
      writeProjectFile(targetPath, readLaravelFeatureFile(file.source, projectName));
    }

    createdItems.push(file.target);
  });

  (feature.copyDirectories || []).forEach((directory) => {
    const sourcePath = path.join(getLaravelFeaturesRoot(), directory.source);
    const targetPath = path.join(projectDirectory, directory.target);

    if (!fs.existsSync(sourcePath)) {
      fail(`Missing Laravel feature directory at ${path.relative(projectRoot, sourcePath)}.`);
    }

    copyTemplateDirectory(sourcePath, targetPath, {
      projectName,
      projectSlug: slugifyProjectName(projectName),
      projectShortName: getProjectShortName(projectName),
      projectDescription: `${projectName} application.`
    });
    createdItems.push(`${directory.target}/`);
  });

  (feature.append || []).forEach((file) => {
    appendUniqueFileContent(
      path.join(projectDirectory, file.target),
      readLaravelFeatureFile(file.source, projectName),
      file.marker
    );
    createdItems.push(file.target);
  });

  return createdItems;
}

function applyLaravelFeatureEnv(projectDirectory, projectName, feature, options = {}) {
  const envEntries = {
    ...(feature.env || {}),
    ...(options.themeConfig || {})
  };

  if (Object.keys(envEntries).length === 0) {
    return [];
  }

  const updatedItems = [];
  const envPathList = [
    path.join(projectDirectory, ".env"),
    path.join(projectDirectory, ".env.example")
  ];

  envPathList.forEach((targetEnvPath) => {
    const lines = fs.existsSync(targetEnvPath) ? fs.readFileSync(targetEnvPath, "utf8").split(/\r?\n/) : [];

    Object.entries(envEntries).forEach(([key, value]) => {
      setEnvValue(lines, key, compileLaravelFeatureContent(String(value), projectName));
    });

    writeProjectFile(targetEnvPath, `${lines.join("\n").replace(/\n+$/, "")}\n`);
    updatedItems.push(path.basename(targetEnvPath));
  });

  return updatedItems;
}

function updateLaravelUserModelForRbac(projectDirectory) {
  const userModelPath = path.join(projectDirectory, "app", "Models", "User.php");

  return replaceFileContent(userModelPath, (content) => {
    let nextContent = content;

    if (!nextContent.includes("use Illuminate\\Database\\Eloquent\\Relations\\BelongsTo;")) {
      nextContent = nextContent.replace(
        /use Illuminate\\Notifications\\Notifiable;\n/,
        "use Illuminate\\Notifications\\Notifiable;\nuse Illuminate\\Database\\Eloquent\\Relations\\BelongsTo;\n"
      );
    }

    if (!nextContent.includes("'role_id'")) {
      nextContent = nextContent.replace(
        /(protected \$fillable = \[\s*)/,
        "$1\n        'role_id',"
      );
    }

    if (!nextContent.includes("public function role(): BelongsTo")) {
      nextContent = nextContent.replace(
        /\n}\s*$/,
        `\n\n    public function role(): BelongsTo\n    {\n        return $this->belongsTo(Role::class);\n    }\n}\n`
      );
    }

    return nextContent;
  });
}

function applyLaravelRbacFeature(projectDirectory) {
  const createdItems = copyLaravelFeatureFiles(projectDirectory, path.basename(projectDirectory), getLaravelFeatureDefinition("rbac"));

  if (updateLaravelUserModelForRbac(projectDirectory)) {
    createdItems.push("app/Models/User.php role relation");
  }

  return createdItems;
}

function applyLaravelProjectStructureFeature(projectDirectory) {
  const feature = getLaravelFeatureDefinition("project-structure");
  const createdItems = [];

  createLaravelBaseStructure(projectDirectory);
  [
    "app/Constants/",
    "app/Jobs/",
    "app/Listeners/",
    "app/Mails/",
    "app/Notifications/",
    "app/Http/Controllers/Admin/Auth/",
    "app/Http/Controllers/Api/V1/",
    "public/assets/css/",
    "public/assets/fonts/",
    "public/assets/images/",
    "public/assets/js/",
    "resources/views/admin/auth/",
    "resources/views/admin/layouts/",
    "resources/views/errors/",
    "resources/views/pages/",
    "routes/api/v1/api.php"
  ].forEach((item) => createdItems.push(item));

  createdItems.push(...copyLaravelFeatureFiles(projectDirectory, path.basename(projectDirectory), feature));

  return {
    createdItems,
    composerUpdated: updateLaravelComposerAutoloadFiles(projectDirectory, feature.autoloadFiles || [])
  };
}

function applyLaravelApiFeature(projectDirectory) {
  const feature = getLaravelFeatureDefinition("api");

  createLaravelBaseStructure(projectDirectory);
  const createdItems = copyLaravelFeatureFiles(projectDirectory, path.basename(projectDirectory), feature);
  updateLaravelRouteServiceProvider(projectDirectory);

  createdItems.push("app/Providers/RouteServiceProvider.php");
  return createdItems;
}

function applyLaravelAdminUiThemeFeature(projectDirectory, projectName, options = {}) {
  const feature = getLaravelFeatureDefinition("admin-ui-theme");

  return [
    ...copyLaravelFeatureFiles(projectDirectory, projectName, feature),
    ...applyLaravelFeatureEnv(projectDirectory, projectName, feature, options)
  ];
}

function applyLaravelAdminPanelFeature(projectDirectory, projectName, options = {}) {
  const feature = getLaravelFeatureDefinition("admin-panel");
  const createdItems = [];

  createdItems.push(...applyLaravelProjectStructureFeature(projectDirectory).createdItems);
  createdItems.push(...copyLaravelFeatureFiles(projectDirectory, projectName, feature));
  createdItems.push(...applyLaravelAdminUiThemeFeature(projectDirectory, projectName, options));

  return createdItems;
}

function applyLaravelAdminAuthFeature(projectDirectory, projectName, options = {}) {
  const feature = getLaravelFeatureDefinition("admin-auth");
  const createdItems = [];

  createdItems.push(...applyLaravelAdminPanelFeature(projectDirectory, projectName, options));
  createdItems.push(...copyLaravelFeatureFiles(projectDirectory, projectName, feature));

  return createdItems;
}

function applyLaravelMiddlewareFeature(projectDirectory) {
  return copyLaravelFeatureFiles(projectDirectory, path.basename(projectDirectory), getLaravelFeatureDefinition("middleware"));
}

function applyLaravelFirebaseFeature(projectDirectory) {
  const feature = getLaravelFeatureDefinition("firebase");

  return [
    ...copyLaravelFeatureFiles(projectDirectory, path.basename(projectDirectory), feature),
    ...applyLaravelFeatureEnv(projectDirectory, path.basename(projectDirectory), feature)
  ];
}

function applyLaravelGenericManifestFeature(projectDirectory, projectName, featureId, options = {}) {
  const feature = getLaravelFeatureDefinition(featureId);

  return {
    createdItems: [
      ...copyLaravelFeatureFiles(projectDirectory, projectName, feature),
      ...applyLaravelFeatureEnv(projectDirectory, projectName, feature, options)
    ],
    composerUpdated: updateLaravelComposerAutoloadFiles(projectDirectory, feature.autoloadFiles || [])
  };
}

function applyLaravelExcelExportFeature(projectDirectory, projectName) {
  return copyLaravelFeatureFiles(projectDirectory, projectName, getLaravelFeatureDefinition("excel-export"));
}

function applyLaravelPdfExportFeature(projectDirectory) {
  return copyLaravelFeatureFiles(projectDirectory, path.basename(projectDirectory), getLaravelFeatureDefinition("pdf-export"));
}

function applyLaravelPwaFeature(projectDirectory, projectName) {
  const pwaSource = path.join(projectRoot, "languages", "laravel", "templates", "public", "pwa");
  const pwaTarget = path.join(projectDirectory, "public", "pwa");

  copyDirectoryContents(pwaSource, pwaTarget, {
    projectName,
    projectSlug: slugifyProjectName(projectName),
    projectShortName: getProjectShortName(projectName),
    projectDescription: `${projectName} progressive web app.`
  });

  return ["public/pwa/"];
}

function applyLaravelFeatureById(projectDirectory, projectName, featureId, databaseConfig = null, options = {}) {
  const feature = getLaravelFeatureDefinition(featureId);

  let createdItems = [];
  let composerUpdated = false;

  if (featureId === "database") {
    if (!databaseConfig) {
      fail("Database feature requires validated database settings.");
    }

    writeLaravelEnvironment(projectDirectory, projectName, databaseConfig);
    createdItems = [".env", ".env.example"];
  } else if (featureId === "rbac") {
    createdItems = applyLaravelRbacFeature(projectDirectory);
  } else if (featureId === "project-structure") {
    const result = applyLaravelProjectStructureFeature(projectDirectory);
    createdItems = result.createdItems;
    composerUpdated = result.composerUpdated;
  } else if (featureId === "api") {
    createdItems = applyLaravelApiFeature(projectDirectory);
  } else if (featureId === "admin-panel") {
    createdItems = applyLaravelAdminPanelFeature(projectDirectory, projectName, options);
  } else if (featureId === "admin-auth") {
    createdItems = applyLaravelAdminAuthFeature(projectDirectory, projectName, options);
  } else if (featureId === "admin-ui-theme") {
    createdItems = applyLaravelAdminUiThemeFeature(projectDirectory, projectName, options);
  } else if (featureId === "middleware") {
    createdItems = applyLaravelMiddlewareFeature(projectDirectory);
  } else if (featureId === "firebase") {
    createdItems = applyLaravelFirebaseFeature(projectDirectory);
  } else if (featureId === "excel-export") {
    createdItems = applyLaravelExcelExportFeature(projectDirectory, projectName);
  } else if (featureId === "pdf-export") {
    createdItems = applyLaravelPdfExportFeature(projectDirectory);
  } else if (featureId === "pwa") {
    createdItems = applyLaravelPwaFeature(projectDirectory, projectName);
  } else {
    const result = applyLaravelGenericManifestFeature(projectDirectory, projectName, featureId, options);
    createdItems = result.createdItems;
    composerUpdated = result.composerUpdated;

    if (featureId === "common-core") {
      if (updateLaravelKernelMiddleware(projectDirectory)) {
        createdItems.push("app/Http/Kernel.php middleware aliases");
      }
    }
  }

  return {
    feature,
    createdItems,
    composerUpdated
  };
}

function writeCrudFile(targetPath, content, force) {
  if (fs.existsSync(targetPath) && !force) {
    fail(`CRUD target already exists: ${path.relative(projectRoot, targetPath)}. Use --force only when you intentionally want to overwrite this module.`);
  }

  writeProjectFile(targetPath, content);
}

function getCrudMigrationFileName(definition) {
  const now = new Date();
  const timestamp = [
    now.getFullYear(),
    String(now.getMonth() + 1).padStart(2, "0"),
    String(now.getDate()).padStart(2, "0"),
    String(now.getHours()).padStart(2, "0"),
    String(now.getMinutes()).padStart(2, "0"),
    String(now.getSeconds()).padStart(2, "0")
  ].join("_");

  return `${timestamp}_create_${definition.tableName}_table.php`;
}

function insertCrudRoute(projectDirectory, routeContent, routeName) {
  const routePath = path.join(projectDirectory, "routes", "web.php");

  if (!fs.existsSync(routePath)) {
    fail("routes/web.php was not found. Create or install the Laravel project before adding CRUD modules.");
  }

  const currentContent = fs.readFileSync(routePath, "utf8");

  if (currentContent.includes(`prefix('${routeName}')`) || currentContent.includes(`prefix("${routeName}")`)) {
    return false;
  }

  const protectedAdminGroupEndPatterns = ["\n    });\n\n});", "\n    });\n});"];
  const protectedAdminGroupIndex = protectedAdminGroupEndPatterns
    .map((pattern) => currentContent.lastIndexOf(pattern))
    .filter((index) => index !== -1)
    .sort((first, second) => second - first)[0] ?? -1;
  const adminGroupIndex = protectedAdminGroupIndex === -1
    ? currentContent.lastIndexOf("\n});")
    : protectedAdminGroupIndex;

  if (!currentContent.includes("Route::prefix('admin')") || adminGroupIndex === -1) {
    fail("Admin route group was not found. Add the Admin Panel or Common Core feature before adding admin CRUD modules.");
  }

  const nextContent = `${currentContent.slice(0, adminGroupIndex)}\n${routeContent.trimEnd()}\n${currentContent.slice(adminGroupIndex)}`;
  writeProjectFile(routePath, nextContent);
  return true;
}

function insertCrudSidebarLink(projectDirectory, definition) {
  const sidebarPath = path.join(projectDirectory, "resources", "views", "admin", "layouts", "sidebar.blade.php");

  if (!fs.existsSync(sidebarPath)) {
    return false;
  }

  const currentContent = fs.readFileSync(sidebarPath, "utf8");

  if (currentContent.includes(`route('admin.${definition.routeName}')`)) {
    return false;
  }

  const link = `                        <li class="nk-menu-item">\n                            <a href="{{ route('admin.${definition.routeName}') }}" class="nk-menu-link">\n                                <span class="nk-menu-icon"><em class="icon ni ni-list"></em></span>\n                                <span class="nk-menu-text">${definition.pluralTitle}</span>\n                            </a>\n                        </li>\n`;
  const marker = "                        <li class=\"nk-menu-item has-sub\">";
  const markerIndex = currentContent.indexOf(marker);
  const nextContent = markerIndex === -1
    ? currentContent.replace(/(\s*<\/ul>)/, `${link}$1`)
    : `${currentContent.slice(0, markerIndex)}${link}${currentContent.slice(markerIndex)}`;

  writeProjectFile(sidebarPath, nextContent);
  return true;
}

function applyLaravelCrudModule(projectDirectory, definition, options = {}) {
  const force = Boolean(options.force);
  const statusPieces = renderCrudStatusPieces(definition);
  const commonVariables = {
    FILLABLE_FIELDS: renderCrudFillableFields(definition.fields),
    CASTS_BLOCK: renderCrudCastsBlock(definition.fields),
    MIGRATION_FIELDS: renderCrudMigrationFields(definition.fields),
    VALIDATION_RULES_CREATE: renderCrudValidationRules(definition.fields),
    VALIDATION_RULES_UPDATE: renderCrudValidationRules(definition.fields),
    DATATABLE_COLUMNS: renderCrudDataTableColumns(definition.fields),
    RAW_COLUMNS: renderCrudRawColumns(definition),
    TABLE_HEADERS: renderCrudTableHeaders(definition.fields),
    TABLE_COLUMNS: renderCrudTableColumns(definition.fields),
    FORM_FIELDS: renderCrudFormFields(definition.fields),
    STATUS_ROUTE: statusPieces.route,
    STATUS_METHOD: statusPieces.method,
    STATUS_SCRIPT: statusPieces.script
  };
  const migrationFileName = getCrudMigrationFileName(definition);
  const files = [
    {
      target: path.join("app", "Models", `${definition.modelClass}.php`),
      content: renderCrudStub("app/Models/Model.php.stub", definition, commonVariables)
    },
    {
      target: path.join("app", "Services", `${definition.serviceClass}.php`),
      content: renderCrudStub("app/Services/Service.php.stub", definition, commonVariables)
    },
    {
      target: path.join("app", "Http", "Controllers", "Admin", `${definition.controllerClass}.php`),
      content: renderCrudStub("app/Http/Controllers/Admin/Controller.php.stub", definition, commonVariables)
    },
    {
      target: path.join("database", "migrations", migrationFileName),
      content: renderCrudStub("database/migrations/create_table.php.stub", definition, commonVariables)
    },
    {
      target: path.join("resources", "views", "admin", definition.viewDirectory, "all.blade.php"),
      content: renderCrudStub("resources/views/admin/__VIEW_DIRECTORY__/all.blade.php.stub", definition, commonVariables)
    },
    {
      target: path.join("resources", "views", "admin", definition.viewDirectory, "add.blade.php"),
      content: renderCrudStub("resources/views/admin/__VIEW_DIRECTORY__/add.blade.php.stub", definition, commonVariables)
    },
    {
      target: path.join("resources", "views", "admin", definition.viewDirectory, "edit.blade.php"),
      content: renderCrudStub("resources/views/admin/__VIEW_DIRECTORY__/edit.blade.php.stub", definition, commonVariables)
    },
    {
      target: path.join("resources", "views", "admin", definition.viewDirectory, "_form.blade.php"),
      content: renderCrudStub("resources/views/admin/__VIEW_DIRECTORY__/_form.blade.php.stub", definition, commonVariables)
    }
  ];

  files.forEach((file) => {
    writeCrudFile(path.join(projectDirectory, file.target), file.content, force);
  });

  const routeAdded = insertCrudRoute(
    projectDirectory,
    renderCrudStub("routes/web.php.stub", definition, commonVariables),
    definition.routeName
  );
  const sidebarAdded = insertCrudSidebarLink(projectDirectory, definition);
  const createdItems = files.map((file) => file.target);

  if (routeAdded) {
    createdItems.push("routes/web.php");
  }

  if (sidebarAdded) {
    createdItems.push("resources/views/admin/layouts/sidebar.blade.php");
  }

  return createdItems;
}

function applyLaravelStarterFeatures(projectDirectory, projectName, choices) {
  const createdItems = [];
  let composerUpdated = false;

  const selectedFeatureIds = ["rbac"];

  if (choices.projectStructure) selectedFeatureIds.push("project-structure");
  if (choices.api) selectedFeatureIds.push("api");
  if (choices.adminPanel) selectedFeatureIds.push("admin-panel");
  if (choices.adminAuth) selectedFeatureIds.push("admin-auth");
  if (choices.adminUiTheme) selectedFeatureIds.push("admin-ui-theme");
  if (choices.adminAssets) selectedFeatureIds.push("admin-assets");
  if (choices.commonCore) selectedFeatureIds.push("common-core");
  if (choices.middleware) selectedFeatureIds.push("middleware");
  if (choices.firebase) selectedFeatureIds.push("firebase");
  if (choices.excelExport) selectedFeatureIds.push("excel-export");
  if (choices.pdfExport) selectedFeatureIds.push("pdf-export");
  if (choices.pwa) selectedFeatureIds.push("pwa");

  selectedFeatureIds.forEach((featureId) => {
    const result = applyLaravelFeatureById(projectDirectory, projectName, featureId, null, {
      themeConfig: choices.themeConfig || {}
    });
    composerUpdated = composerUpdated || result.composerUpdated;
    createdItems.push(...result.createdItems);
  });

  return {
    createdItems,
    composerUpdated
  };
}

function replaceFileContent(filePath, transform) {
  if (!fs.existsSync(filePath)) {
    return false;
  }

  const currentContent = fs.readFileSync(filePath, "utf8");
  const nextContent = transform(currentContent);

  if (nextContent === currentContent) {
    return true;
  }

  fs.writeFileSync(filePath, nextContent, { mode: 0o644 });
  fs.chmodSync(filePath, 0o644);
  return true;
}

function updateLaravelTimezone(projectDirectory, timezone) {
  const appConfigPath = path.join(projectDirectory, "config", "app.php");

  return replaceFileContent(appConfigPath, (content) => {
    return content.replace(/'timezone'\s*=>\s*'[^']*'/, `'timezone' => '${timezone}'`);
  });
}

function addPhpArrayEntry(content, arrayName, entry) {
  if (content.includes(entry)) {
    return content;
  }

  if (arrayName === "providers" && content.includes("ServiceProvider::defaultProviders()->merge([")) {
    return content.replace(
      /('providers'\s*=>\s*ServiceProvider::defaultProviders\(\)->merge\(\[)/,
      `$1\n        ${entry},`
    );
  }

  if (arrayName === "aliases" && content.includes("Facade::defaultAliases()->merge([")) {
    return content.replace(
      /('aliases'\s*=>\s*Facade::defaultAliases\(\)->merge\(\[)/,
      `$1\n        ${entry},`
    );
  }

  return content.replace(
    new RegExp(`('${arrayName}'\\s*=>\\s*\\[)`),
    `$1\n        ${entry},`
  );
}

function updateLaravelPackageProvidersAndAliases(projectDirectory) {
  const appConfigPath = path.join(projectDirectory, "config", "app.php");

  return replaceFileContent(appConfigPath, (content) => {
    let nextContent = content;

    [
      "Maatwebsite\\Excel\\ExcelServiceProvider::class",
      "Tymon\\JWTAuth\\Providers\\LaravelServiceProvider::class",
      "Yajra\\DataTables\\DataTablesServiceProvider::class",
      "Barryvdh\\DomPDF\\ServiceProvider::class"
    ].forEach((provider) => {
      nextContent = addPhpArrayEntry(nextContent, "providers", provider);
    });

    [
      "'Excel' => Maatwebsite\\Excel\\Facades\\Excel::class",
      "'JWTAuth' => Tymon\\JWTAuth\\Facades\\JWTAuth::class",
      "'JWTFactory' => Tymon\\JWTAuth\\Facades\\JWTFactory::class",
      "'DataTables' => Yajra\\DataTables\\Facades\\DataTables::class",
      "'PDF' => Barryvdh\\DomPDF\\Facade\\Pdf::class"
    ].forEach((alias) => {
      nextContent = addPhpArrayEntry(nextContent, "aliases", alias);
    });

    return nextContent;
  });
}

function updateLaravelJwtAuthGuard(projectDirectory) {
  const authConfigPath = path.join(projectDirectory, "config", "auth.php");

  return replaceFileContent(authConfigPath, (content) => {
    if (content.includes("'driver' => 'jwt'") && content.includes("'api' => [")) {
      return content;
    }

    const apiGuard = `\n\n        'api' => [\n            'driver' => 'jwt',\n            'provider' => 'users',\n        ],`;
    const webGuardPattern = /('web'\s*=>\s*\[\s*'driver'\s*=>\s*'session',\s*'provider'\s*=>\s*'users',\s*\],)/;

    if (webGuardPattern.test(content)) {
      return content.replace(webGuardPattern, `$1${apiGuard}`);
    }

    return content.replace(/('guards'\s*=>\s*\[)/, `$1${apiGuard}`);
  });
}

function updateLaravelKernelMiddleware(projectDirectory) {
  const kernelPath = path.join(projectDirectory, "app", "Http", "Kernel.php");

  return replaceFileContent(kernelPath, (content) => {
    let nextContent = content;

    [
      "\\App\\Http\\Middleware\\NormalizeApiResponse::class",
      "\\App\\Http\\Middleware\\ApiLoggerMiddleware::class",
      "\\App\\Http\\Middleware\\SecureHeaders::class",
      "\\App\\Http\\Middleware\\SanitizeInput::class"
    ].forEach((middleware) => {
      if (!nextContent.includes(middleware)) {
        nextContent = nextContent.replace(
          /('api'\s*=>\s*\[[\s\S]*?\\Illuminate\\Routing\\Middleware\\SubstituteBindings::class,)/,
          `$1\n            ${middleware},`
        );
      }
    });

    [
      "'api.auth' => \\App\\Http\\Middleware\\ApiAuthenticate::class",
      "'app.maintenance' => \\App\\Http\\Middleware\\AppMaintenance::class",
      "'admin.maintenance' => \\App\\Http\\Middleware\\AdminMaintenance::class"
    ].forEach((middlewareAlias) => {
      if (!nextContent.includes(middlewareAlias)) {
        nextContent = nextContent.replace(
          /(protected \$routeMiddleware\s*=\s*\[[\s\S]*?'verified'\s*=>\s*\\Illuminate\\Auth\\Middleware\\EnsureEmailIsVerified::class,)/,
          `$1\n        ${middlewareAlias},`
        );
      }
    });

    return nextContent;
  });
}

function updateLaravelRouteServiceProvider(projectDirectory) {
  const providerPath = path.join(projectDirectory, "app", "Providers", "RouteServiceProvider.php");

  return replaceFileContent(providerPath, (content) => {
    const requestedApiRouteBlock = `Route::prefix('api')
                ->middleware(['api'])
                ->namespace($this->namespace)
                ->group(function () {
                    require base_path('routes/api/v1/api.php');
                });`;

    if (content.includes("routes/api/v1/api.php")) {
      return content;
    }

    let nextContent = content;

    if (!nextContent.includes("protected $namespace = 'App\\\\Http\\\\Controllers';")) {
      nextContent = nextContent.replace(
        /class RouteServiceProvider extends ServiceProvider\s*\{/,
        "class RouteServiceProvider extends ServiceProvider\n{\n    protected $namespace = 'App\\\\Http\\\\Controllers';\n"
      );
    }

    if (/Route::middleware\('api'\)\s*->prefix\('api'\)\s*->group\(base_path\('routes\/api.php'\)\);/.test(nextContent)) {
      return nextContent.replace(
        /Route::middleware\('api'\)\s*->prefix\('api'\)\s*->group\(base_path\('routes\/api.php'\)\);/,
        requestedApiRouteBlock
      );
    }

    return nextContent.replace(
      /Route::[^;]*base_path\('routes\/api.php'\)[^;]*;/s,
      requestedApiRouteBlock
    );
  });
}

function setEnvValue(lines, key, value) {
  const nextLine = `${key}=${value}`;
  const index = lines.findIndex((line) => line.startsWith(`${key}=`));

  if (index === -1) {
    lines.push(nextLine);
    return;
  }

  lines[index] = nextLine;
}

function writeLaravelEnvironment(projectDirectory, projectName, databaseConfig) {
  const envExamplePath = path.join(projectDirectory, ".env.example");
  const envPath = path.join(projectDirectory, ".env");
  const sourcePath = fs.existsSync(envExamplePath) ? envExamplePath : envPath;
  const defaultEnvironment = [
    "APP_NAME=Laravel",
    "APP_ENV=local",
    "APP_KEY=",
    "APP_DEBUG=true",
    "APP_URL=http://localhost",
    "",
    "LOG_CHANNEL=stack",
    "LOG_DEPRECATIONS_CHANNEL=null",
    "LOG_LEVEL=debug",
    "",
    "DB_CONNECTION=mysql",
    "DB_HOST=127.0.0.1",
    "DB_PORT=3306",
    "DB_DATABASE=laravel",
    "DB_USERNAME=root",
    "DB_PASSWORD=",
    "",
    "BROADCAST_DRIVER=log",
    "CACHE_DRIVER=file",
    "FILESYSTEM_DISK=local",
    "QUEUE_CONNECTION=sync",
    "SESSION_DRIVER=file",
    "SESSION_LIFETIME=120",
    "",
    "JWT_SECRET="
  ].join("\n");
  const sourceContent = fs.existsSync(sourcePath)
    ? fs.readFileSync(sourcePath, "utf8")
    : defaultEnvironment;
  const lines = sourceContent.split(/\r?\n/);

  setEnvValue(lines, "APP_NAME", `"${projectName}"`);
  setEnvValue(lines, "APP_ENV", "local");
  setEnvValue(lines, "APP_DEBUG", "true");
  setEnvValue(lines, "APP_URL", "http://localhost");

  Object.entries(databaseConfig).forEach(([key, value]) => {
    setEnvValue(lines, key, value);
  });

  setEnvValue(lines, "JWT_SECRET", "");

  const content = `${lines.join("\n").replace(/\n+$/, "")}\n`;

  fs.writeFileSync(envExamplePath, content, { mode: 0o644 });
  fs.writeFileSync(envPath, content, { mode: 0o644 });
}

function runLaravelSetupCommands(projectDirectory, setupCommands) {
  setupCommands.forEach((setupCommand) => {
    const [command, ...args] = setupCommand;

    runCommand(command, args, {
      cwd: projectDirectory,
      failureMessage: `Laravel setup command failed: ${command} ${args.join(" ")}`
    });
  });
}

async function createLaravelProject(projectName, config, manifest, projectDirectory, outputRoot, skipInstall, args) {
  const generator = manifest.generator;
  const totalSteps = skipInstall ? 10 : 20;

  console.log("");
  writeProgress(1, totalSteps, "Checking PHP and Composer requirements", `Laravel ${generator.laravelVersion} requires PHP ${generator.minimumPhpVersion} or higher.`);
  const requirements = checkLaravelRequirements(generator);
  console.log(`  PHP: ${requirements.phpVersion}`);
  console.log(`  Composer: ${requirements.composerVersion}`);

  writeProgress(2, totalSteps, "Selecting Laravel timezone", "DevForge will update config/app.php after the project is available.");
  const timezone = await collectLaravelTimezone(args);
  console.log(`  Timezone: ${timezone}`);

  writeProgress(3, totalSteps, "Collecting and validating database settings", "DevForge will verify the database connection before continuing.");
  const databaseConfig = await collectAndValidateLaravelDatabaseConfig(projectName, args);

  if (!skipInstall) {
    const confirmed = await askForSensitiveConfirmation(config, manifest, hasOption(args, "--yes"));

    if (!confirmed) {
      fail("Laravel installation cancelled before running Composer commands.");
    }
  }

  fs.mkdirSync(outputRoot, { recursive: true, mode: 0o755 });
  fs.chmodSync(outputRoot, 0o755);

  if (skipInstall) {
    fs.mkdirSync(projectDirectory, { recursive: true, mode: 0o755 });
    fs.chmodSync(projectDirectory, 0o755);
    writeProgress(4, totalSteps, "Created project directory", path.relative(projectRoot, projectDirectory));
  } else {
    writeProgress(4, totalSteps, "Creating Laravel application skeleton", `${generator.createProject.command} ${generator.createProject.args.join(" ")}`);
    runCommand(generator.createProject.command, [...generator.createProject.args, projectDirectory], {
      cwd: projectRoot,
      failureMessage: "Laravel project skeleton creation failed"
    });

    writeProgress(5, totalSteps, "Preparing Composer security settings", "Setting audit.block-insecure=false inside the generated Laravel project.");
    runCommand("composer", ["config", "audit.block-insecure", String(generator.composerAuditBlockInsecure)], {
      cwd: projectDirectory,
      failureMessage: "Laravel Composer audit configuration failed"
    });

    writeProgress(6, totalSteps, "Installing Laravel framework dependencies", "Running Composer install for Laravel 10.");
    runCommand("composer", ["install"], {
      cwd: projectDirectory,
      failureMessage: "Laravel framework dependency installation failed"
    });
  }

  writeProgress(skipInstall ? 5 : 7, totalSteps, "Applying database configuration", "Updating .env and .env.example.");
  writeLaravelEnvironment(projectDirectory, projectName, databaseConfig);

  if (!skipInstall) {
    writeProgress(8, totalSteps, "Generating Laravel application key", "Running php artisan key:generate.");
    runCommand("php", ["artisan", "key:generate", "--force"], {
      cwd: projectDirectory,
      failureMessage: "Laravel application key generation failed"
    });
  }

  writeProgress(skipInstall ? 6 : 9, totalSteps, "Configuring Laravel timezone", `Setting config/app.php timezone to ${timezone}.`);
  const timezoneUpdated = updateLaravelTimezone(projectDirectory, timezone);
  console.log(`  ${timezoneUpdated ? "config/app.php updated." : "config/app.php is not available in skip-install mode."}`);

  writeProgress(skipInstall ? 7 : 10, totalSteps, "Applying Laravel hosting files", "Adding root .htaccess, root index.php, and DevForge notes.");
  copyTemplateDirectory(path.join(projectRoot, config.languagesDirectory, "laravel", manifest.entry.templates), projectDirectory, {
    projectName,
    projectSlug: slugifyProjectName(projectName)
  }, {
    ignorePaths: ["app/Exceptions", "public/pwa"]
  });

  writeProgress(skipInstall ? 8 : 11, totalSteps, "Handling Exceptions", "Applying starter API and JWT exception responses.");
  const handlerUpdated = copyLaravelExceptionHandler(projectDirectory);
  console.log(`  ${handlerUpdated ? "Updated app/Exceptions/Handler.php." : "Handler template was not found."}`);

  writeProgress(skipInstall ? 9 : 12, totalSteps, "Configuring Laravel starter folders", "Excel exports are always prepared; optional features are selected interactively.");
  const featureChoices = await collectLaravelStarterFeatureChoices(projectName);
  const featureResult = applyLaravelStarterFeatures(projectDirectory, projectName, featureChoices);
  console.log(`  Created: ${featureResult.createdItems.join(", ")}`);

  if (skipInstall) {
    writeProgress(10, totalSteps, "Skipped Composer package installation", "Files were prepared without installing Laravel dependencies.");
    return;
  }

  writeProgress(13, totalSteps, "Configuring API Routes", "Loading routes/api/v1/api.php through RouteServiceProvider before package discovery runs.");
  createLaravelBaseStructure(projectDirectory);
  const routeProviderUpdated = updateLaravelRouteServiceProvider(projectDirectory);
  console.log(`  ${routeProviderUpdated ? "RouteServiceProvider updated." : "RouteServiceProvider was not found."}`);

  writeProgress(14, totalSteps, "Installing Laravel starter packages", "Installing UI, Excel, Google Auth, JWT, DataTables, DomPDF, and Guzzle.");
  runCommand("composer", ["require", ...generator.packages, ...(generator.composerRequireOptions || [])], {
    cwd: projectDirectory,
    failureMessage: "Laravel package installation failed"
  });

  writeProgress(15, totalSteps, "Configuring Package Providers and Aliases", "Adding Excel, JWT, DataTables, and DomPDF entries to config/app.php.");
  const appConfigUpdated = updateLaravelPackageProvidersAndAliases(projectDirectory);
  console.log(`  ${appConfigUpdated ? "config/app.php updated." : "config/app.php was not found."}`);

  if (featureResult.composerUpdated) {
    writeProgress(16, totalSteps, "Refreshing Composer autoload files", "Registering selected helper files.");
    runCommand("composer", ["dump-autoload"], {
      cwd: projectDirectory,
      failureMessage: "Composer autoload refresh failed"
    });
  } else {
    writeProgress(16, totalSteps, "Composer autoload refresh not required", "No helper files were selected for autoload registration.");
  }

  writeProgress(17, totalSteps, "Publishing package configuration", "Creating Excel, JWT, DataTables, and DomPDF config files where supported.");
  runLaravelSetupCommands(projectDirectory, generator.setupCommands.slice(0, 5));

  writeProgress(18, totalSteps, "Configuring JWT API Guard", "Adding the api guard with jwt driver in config/auth.php.");
  const authUpdated = updateLaravelJwtAuthGuard(projectDirectory);
  console.log(`  ${authUpdated ? "config/auth.php updated." : "config/auth.php was not found."}`);

  writeProgress(19, totalSteps, "Creating storage link and clearing caches", "Linking public storage and clearing cached framework files.");
  runLaravelSetupCommands(projectDirectory, generator.setupCommands.slice(5, 6));
  runLaravelSetupCommands(projectDirectory, generator.setupCommands.slice(6));

  writeProgress(20, totalSteps, "Verifying Laravel installation", "Checking installed package metadata.");
  runCommand("composer", ["show", "laravel/framework"], {
    cwd: projectDirectory,
    failureMessage: "Laravel framework verification failed"
  });
}

function printNextSteps(projectPath, manifest, skippedInstall) {
  const nextSteps = (manifest.nextSteps || []).map((nextStep) => {
    return nextStep.replaceAll("__PROJECT_PATH__", projectPath);
  });
  const installStep = manifest.install
    ? `${manifest.install.command} ${(manifest.install.args || []).join(" ")}`
    : null;

  nextSteps.forEach((nextStep, index) => {
    console.log(`  ${nextStep}`);

    if (index === 0 && skippedInstall && manifest.install && manifest.install.enabled && installStep) {
      console.log(`  ${installStep}`);
    }
  });
}

function assertValidProjectName(projectName) {
  if (!projectName) {
    fail("PROJECT_NAME is required.");
  }

  if (!/^[a-zA-Z0-9._-]+$/.test(projectName)) {
    fail("PROJECT_NAME can only contain letters, numbers, dots, underscores, and hyphens.");
  }

  if (projectName === "." || projectName === "..") {
    fail("PROJECT_NAME cannot be . or ..");
  }
}

async function createProject(args) {
  const projectName = args[1];
  const config = readConfig();
  const language = getOptionValue(args, config.languageOptionName);
  const skipInstall = hasOption(args, "--skip-install");

  assertValidProjectName(projectName);

  if (!language) {
    fail(`${config.languageOptionName} is required. Example: ${config.commandName} create ${projectName} ${config.languageOptionName} laravel`);
  }

  const languageDirectory = path.join(projectRoot, config.languagesDirectory, language);
  const manifestPath = path.join(languageDirectory, "manifest.json");

  if (!fs.existsSync(languageDirectory)) {
    fail(`"${language}" is not supported yet. Available templates: ${formatSupportedTemplates(config)}. Add new support by creating ${config.languagesDirectory}/${language}/ with a manifest.json file.`);
  }

  if (!fs.existsSync(manifestPath)) {
    fail(`Missing template manifest at ${path.relative(projectRoot, manifestPath)}.`);
  }

  const outputRoot = path.join(projectRoot, config.defaultOutputDirectory);
  const projectDirectory = path.join(outputRoot, projectName);
  const manifest = readJsonFile(manifestPath);

  if (!manifest.entry || !manifest.entry.templates) {
    fail(`Template manifest is missing entry.templates at ${path.relative(projectRoot, manifestPath)}.`);
  }

  const templateDirectory = path.join(languageDirectory, manifest.entry.templates);
  const shouldInstall = manifest.install && manifest.install.enabled && !skipInstall;
  const totalSteps = shouldInstall ? 6 : 4;

  if (fs.existsSync(projectDirectory)) {
    fail(`A project named "${projectName}" already exists at ${path.relative(projectRoot, projectDirectory)}.`);
  }

  console.log("");
  console.log(`${config.appName} is creating a new starter project`);
  console.log(`Project name: ${projectName}`);
  console.log(`Template: ${manifest.name}`);
  console.log(`Destination: ${path.relative(projectRoot, projectDirectory)}`);
  if (manifest.generator && manifest.generator.type === "laravel-composer") {
    console.log(`Laravel installation: ${skipInstall ? "skipped for this command" : "will run with Composer"}`);
  } else {
    console.log(`Package installation: ${shouldInstall ? "will run after files are created" : "skipped for this command"}`);
  }
  console.log("");

  if (manifest.generator && manifest.generator.type === "laravel-composer") {
    await createLaravelProject(projectName, config, manifest, projectDirectory, outputRoot, skipInstall, args);

    console.log("");
    console.log("Laravel project created successfully.");
    console.log(`Location: ${path.relative(projectRoot, projectDirectory)}`);
    console.log("Permissions: 755 for generated directories and 644 for DevForge-managed files.");
    console.log("");
    console.log("Next steps:");
    printNextSteps(path.relative(projectRoot, projectDirectory), manifest, skipInstall);
    console.log("");
    return;
  }

  writeProgress(1, totalSteps, "Validated project request", `Using ${path.relative(projectRoot, manifestPath)}`);

  fs.mkdirSync(projectDirectory, {
    recursive: true,
    mode: 0o755
  });

  fs.chmodSync(outputRoot, 0o755);
  fs.chmodSync(projectDirectory, 0o755);

  writeProgress(2, totalSteps, "Created project directory", path.relative(projectRoot, projectDirectory));

  copyTemplateDirectory(templateDirectory, projectDirectory, {
    projectName,
    projectSlug: slugifyProjectName(projectName)
  });

  writeProgress(3, totalSteps, "Copied template files", path.relative(projectRoot, templateDirectory));

  writeProgress(4, totalSteps, "Applied project configuration", "Project placeholders replaced successfully.");

  if (shouldInstall) {
    writeProgress(5, totalSteps, "Installing dependencies", `${manifest.install.command} ${(manifest.install.args || []).join(" ")}`);
    installDependencies(projectDirectory, manifest);
    writeProgress(6, totalSteps, "Installed dependencies", "Package installation completed successfully.");
  }

  console.log("");
  console.log("Project folder created successfully.");
  console.log(`Location: ${path.relative(projectRoot, projectDirectory)}`);
  console.log("Permissions: 755");
  console.log("");
  console.log("Next steps:");
  printNextSteps(path.relative(projectRoot, projectDirectory), manifest, skipInstall);
  console.log("");
}

async function addLaravelFeatureCommand(args) {
  const config = readConfig();

  if (hasOption(args, "--list") || args[1] === "--list") {
    printLaravelFeatureList();
    console.log("");
    console.log(`Add a skipped feature later with: ${config.commandName} feature PROJECT_NAME`);
    console.log(`Add one directly with: ${config.commandName} feature PROJECT_NAME --feature FEATURE_ID`);
    console.log("");
    return;
  }

  const projectName = args[1];
  assertValidProjectName(projectName);

  const projectDirectory = path.join(projectRoot, config.defaultOutputDirectory, projectName);

  if (!fs.existsSync(projectDirectory)) {
    fail(`Project "${projectName}" was not found at ${path.relative(projectRoot, projectDirectory)}.`);
  }

  const registry = getLaravelFeatureRegistry();
  const directFeatureId = getOptionValue(args, "--feature");

  console.log("");
  console.log(`${config.appName} Laravel feature setup`);
  console.log(`Project: ${projectName}`);
  console.log(`Location: ${path.relative(projectRoot, projectDirectory)}`);

  const applyFeature = async (featureId) => {
    let databaseConfig = null;
    let featureOptions = {};

    if (featureId === "database") {
      console.log("");
      console.log("Database Configuration feature");
      console.log("DevForge will ask for database details one by one, validate the connection, then update .env and .env.example.");
      databaseConfig = await collectAndValidateLaravelDatabaseConfig(projectName, args);
    }

    if (["admin-ui-theme", "admin-panel", "admin-auth", "common-core"].includes(featureId)) {
      if (!process.stdin.isTTY) {
        featureOptions = {};
      } else {
        const prompt = createPrompt();

        try {
          featureOptions = {
            themeConfig: await collectLaravelUiThemeConfig(prompt, projectName)
          };
        } finally {
          prompt.close();
        }
      }
    }

    const featureResult = applyLaravelFeatureById(projectDirectory, projectName, featureId, databaseConfig, featureOptions);

    console.log("");
    writeProgress(1, 1, `Applied ${featureResult.feature.title}`, featureResult.feature.description);
    console.log(`  Updated: ${featureResult.createdItems.join(", ")}`);

    if (featureResult.composerUpdated) {
      console.log("  Composer autoload files were updated. Run composer dump-autoload inside the project if dependencies are already installed.");
    }
  };

  if (directFeatureId) {
    await applyFeature(directFeatureId);
    console.log("");
    console.log("Feature setup completed successfully.");
    return;
  }

  if (!process.stdin.isTTY) {
    fail("Interactive feature selection requires a terminal. Use --feature FEATURE_ID for non-interactive runs.");
  }

  const prompt = createPrompt();

  try {
    let continueAdding = true;

    while (continueAdding) {
      printLaravelFeatureList();
      console.log("");
      const selectedFeature = await askNumberChoice(prompt, "Select feature number", registry);
      await applyFeature(selectedFeature.id);
      continueAdding = await askYesNo(prompt, "Do you want to add any other feature?", "no");
    }
  } finally {
    prompt.close();
  }

  console.log("");
  console.log("Feature setup completed successfully.");
}

async function collectCrudFieldInput(args) {
  const directFields = getOptionValue(args, "--fields");

  if (directFields) {
    return directFields;
  }

  if (!process.stdin.isTTY) {
    return "name:string,description:text,is_active:boolean";
  }

  const prompt = createPrompt();

  try {
    console.log("");
    console.log(color.bold("CRUD Field Setup"));
    console.log(color.dim("Define fields once and DevForge will reuse them for migration, model fillable, validation, form inputs, and DataTable columns."));
    console.log(color.dim("Supported types: string, text, integer, decimal, boolean, date, datetime, email, url."));
    return await askQuestion(prompt, "Enter fields", "name:string,description:text,is_active:boolean");
  } finally {
    prompt.close();
  }
}

async function addLaravelCrudCommand(args) {
  const config = readConfig();

  if (hasOption(args, "--help") || hasOption(args, "-h") || args.length < 3) {
    console.log(`${config.appName} Laravel CRUD generator`);
    console.log("");
    console.log("Usage:");
    console.log(`  ${config.commandName} crud PROJECT_NAME MODULE_NAME`);
    console.log(`  ${config.commandName} crud PROJECT_NAME MODULE_NAME --fields name:string,description:text,is_active:boolean`);
    console.log("");
    console.log("Example:");
    console.log(`  ${config.commandName} crud blog-api Category --fields name:string,description:text,is_active:boolean`);
    console.log("");
    console.log("Options:");
    console.log("  --fields LIST  Field list in name:type format.");
    console.log("  --force        Overwrite existing CRUD files for this module.");
    return;
  }

  const projectName = args[1];
  const moduleName = args[2];
  assertValidProjectName(projectName);

  const projectDirectory = path.join(projectRoot, config.defaultOutputDirectory, projectName);

  if (!fs.existsSync(projectDirectory)) {
    fail(`Project "${projectName}" was not found at ${path.relative(projectRoot, projectDirectory)}.`);
  }

  const fieldInput = await collectCrudFieldInput(args);
  const definition = buildLaravelCrudDefinition(moduleName, fieldInput);

  console.log("");
  console.log(`${config.appName} Laravel CRUD setup`);
  console.log(`Project: ${projectName}`);
  console.log(`Location: ${path.relative(projectRoot, projectDirectory)}`);
  console.log("");
  console.log("CRUD naming definition");
  console.log(`  Module: ${definition.singularTitle}`);
  console.log(`  Model: ${definition.modelClass}`);
  console.log(`  Controller: ${definition.controllerClass}`);
  console.log(`  Service: ${definition.serviceClass}`);
  console.log(`  Table: ${definition.tableName}`);
  console.log(`  Route: admin/${definition.routeName}`);
  console.log(`  Views: resources/views/admin/${definition.viewDirectory}`);
  console.log(`  Fields: ${definition.fields.map((field) => `${field.name}:${field.type}`).join(", ")}`);

  writeProgress(1, 4, "Preparing CRUD module", "DevForge derived all names from the module definition.");
  writeProgress(2, 4, "Rendering CRUD templates", "Model, service, controller, migration, views, routes, and sidebar link are generated from stubs.");
  const createdItems = applyLaravelCrudModule(projectDirectory, definition, {
    force: hasOption(args, "--force")
  });
  writeProgress(3, 4, "Writing CRUD files", `${createdItems.length} project files were created or updated.`);
  writeProgress(4, 4, "CRUD module ready", `${definition.pluralTitle} admin CRUD is available at admin/${definition.routeName}/all.`);

  console.log("");
  console.log("CRUD setup completed successfully.");
  console.log("Updated:");
  createdItems.forEach((item) => console.log(`  ${item}`));
  console.log("");
  console.log("Next steps:");
  console.log("  php artisan migrate");
  console.log(`  Open /admin/${definition.routeName}/all after logging in.`);
}

async function main() {
  const config = readConfig();
  const args = process.argv.slice(2);
  const command = args[0];

  if (!command || command === "--help" || command === "-h") {
    printUsage(config);
    return;
  }

  if (command === "create") {
    await createProject(args);
    return;
  }

  if (command === "feature") {
    await addLaravelFeatureCommand(args);
    return;
  }

  if (command === "crud") {
    await addLaravelCrudCommand(args);
    return;
  }

  fail(`Unknown command "${command}".`);
}

main();
