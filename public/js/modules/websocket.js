// WebSocket Client class
export class WebSocketClient {
    constructor(url, options = {}) {
        this.url = url;
        this.options = {
            reconnectInterval: 3000,
            maxReconnectAttempts: 5,
            ...options
        };
        this.reconnectAttempts = 0;
        this.handlers = new Map();
        this.connect();
    }

    connect() {
        this.ws = new WebSocket(this.url);
        
        this.ws.onopen = () => {
            console.log('WebSocket Connected');
            this.reconnectAttempts = 0;
            if (this.handlers.has('connect')) {
                this.handlers.get('connect')();
            }
        };

        this.ws.onclose = () => {
            console.log('WebSocket Disconnected');
            if (this.reconnectAttempts < this.options.maxReconnectAttempts) {
                setTimeout(() => {
                    this.reconnectAttempts++;
                    this.connect();
                }, this.options.reconnectInterval);
            }
        };

        this.ws.onmessage = (event) => {
            const data = JSON.parse(event.data);
            if (this.handlers.has(data.type)) {
                this.handlers.get(data.type)(data);
            }
        };

        this.ws.onerror = (error) => {
            console.error('WebSocket Error:', error);
        };
    }

    on(type, handler) {
        this.handlers.set(type, handler);
    }

    send(data) {
        if (this.ws.readyState === WebSocket.OPEN) {
            this.ws.send(JSON.stringify(data));
        }
    }
}