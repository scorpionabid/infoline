export const LoggerConfig = {
  SERVER_ERROR_ENDPOINT: "/log/client-error",
  DEBUG_MODE: process.env.APP_DEBUG || false,
  LOG_LEVELS: {
    INFO: "info",
    ERROR: "error",
    WARN: "warn",
  },
};
