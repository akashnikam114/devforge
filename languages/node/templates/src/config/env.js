require('dotenv').config();

const env = {
  appName: process.env.APP_NAME || '__PROJECT_NAME__',
  nodeEnv: process.env.NODE_ENV || 'development',
  port: Number(process.env.PORT || 3000),
};

module.exports = { env };

