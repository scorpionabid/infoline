class ErrorTracker {
  static init() {
    // Qlobal xəta tutucusu
    window.addEventListener("error", this.handleGlobalError);

    // Promise xətaları
    window.addEventListener("unhandledrejection", this.handlePromiseRejection);
  }

  static handleGlobalError(event) {
    const errorDetails = {
      message: event.message,
      filename: event.filename,
      lineno: event.lineno,
      colno: event.colno,
      stack: event.error ? event.error.stack : null,
    };

    // Servərə göndər
    this.sendErrorToServer(errorDetails);
  }

  static handlePromiseRejection(event) {
    const errorDetails = {
      message: event.reason.message,
      stack: event.reason.stack,
    };

    this.sendErrorToServer(errorDetails);
  }

  static sendErrorToServer(errorDetails) {
    fetch("/log/client-error", {
      method: "POST",
      headers: {
        "Content-Type": "application/json",
        "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
          .content,
      },
      body: JSON.stringify({
        ...errorDetails,
        url: window.location.href,
        user_id: window.userId || "anonymous",
      }),
    });
  }

  // Spesifik button üçün error tracking
  static trackButtonClick(buttonSelector, actionCallback) {
    const button = document.querySelector(buttonSelector);

    button.addEventListener("click", async (event) => {
      try {
        await actionCallback(event);
      } catch (error) {
        this.handleSpecificButtonError(button, error);
      }
    });
  }

  static handleSpecificButtonError(button, error) {
    const errorDetails = {
      buttonText: button.textContent,
      buttonId: button.id,
      buttonClasses: button.className,
      errorMessage: error.message,
      stack: error.stack,
    };

    this.sendErrorToServer(errorDetails);
  }
}

// Sistemi başlat
ErrorTracker.init();
