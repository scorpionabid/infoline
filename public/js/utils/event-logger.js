class EventLogger {
  static init() {
    this.logContainer = this.createLogContainer();
    this.attachGlobalListeners();
  }

  static createLogContainer() {
    const container = document.createElement("div");
    container.id = "event-logger";
    container.style.cssText = `
            position: fixed;
            bottom: 20px;
            right: 20px;
            width: 300px;
            max-height: 400px;
            overflow-y: auto;
            background: rgba(0,0,0,0.8);
            color: white;
            padding: 10px;
            z-index: 10000;
            font-family: monospace;
            border-radius: 5px;
        `;
    document.body.appendChild(container);
    return container;
  }

  static log(message, type = "info") {
    const logEntry = document.createElement("div");
    logEntry.innerHTML = `
            <span style="color: ${this.getColorByType(type)}">
                [${type.toUpperCase()}] ${new Date().toLocaleTimeString()} - ${message}
            </span>
        `;
    this.logContainer.prepend(logEntry);

    // Konsola da əlavə et
    console[type](message);
  }

  static getColorByType(type) {
    const colors = {
      info: "#00ff00",
      error: "#ff0000",
      warn: "#ffff00",
      debug: "#00ffff",
    };
    return colors[type] || "#ffffff";
  }

  static attachGlobalListeners() {
    document.addEventListener("click", (event) => {
      const target = event.target;

      // Button üçün detaylı log
      if (target.tagName === "BUTTON" || target.closest("button")) {
        const button =
          target.tagName === "BUTTON" ? target : target.closest("button");

        this.log(`Button Click Detected: ${button.textContent}`, "info");
        this.logButtonDetails(button);
      }
    });
  }

  static logButtonDetails(button) {
    // Düymənin bütün atributlarını log et
    const attributes = {};
    for (let i = 0; i < button.attributes.length; i++) {
      const attr = button.attributes[i];
      attributes[attr.name] = attr.value;
    }

    // Əlavə məlumatlar
    const details = {
      text: button.textContent,
      className: button.className,
      disabled: button.disabled,
      type: button.type,
      attributes: attributes,
    };

    this.log(JSON.stringify(details, null, 2), "debug");
  }

  static trackAjaxCalls() {
    const originalXHR = XMLHttpRequest.prototype.open;
    XMLHttpRequest.prototype.open = function (...args) {
      this.addEventListener("load", () => {
        EventLogger.log(`AJAX Call: ${args[0]} ${args[1]}`, "info");
        EventLogger.log(`Response Status: ${this.status}`, "debug");
      });
      return originalXHR.apply(this, args);
    };
  }

  static enableDetailedErrorTracking() {
    window.addEventListener("error", (event) => {
      this.log(`Unhandled Error: ${event.message}`, "error");
      this.log(`Source: ${event.filename}:${event.lineno}`, "error");
    });
  }
}

// Sistemi başlat
document.addEventListener("DOMContentLoaded", () => {
  EventLogger.init();
  EventLogger.trackAjaxCalls();
  EventLogger.enableDetailedErrorTracking();
});
