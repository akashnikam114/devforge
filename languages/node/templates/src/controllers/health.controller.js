function getHealth(request, response) {
  response.status(200).json({
    success: true,
    message: '__PROJECT_NAME__ is running',
    uptime: process.uptime(),
    timestamp: new Date().toISOString(),
  });
}

module.exports = { getHealth };

