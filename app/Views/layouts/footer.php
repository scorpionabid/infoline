    <!-- Footer -->
    <footer class="footer mt-5 py-3 bg-white border-top">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <span class="text-muted"> 2024 InfoLine. Bütün hüquqlar qorunur.</span>
                </div>
                <div class="col-md-6 text-end">
                    <span class="text-muted">Versiya: 1.0.0</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- JavaScript -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.4/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.bootstrap5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.html5.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.3.6/js/buttons.print.min.js"></script>
    <script>
        window.appConfig = {
            baseUrl: '<?php echo $_ENV['APP_URL']; ?>',
            wsPort: <?php echo $_ENV['WS_PORT']; ?>,
            user: {
                id: <?php echo $_SESSION['user_id']; ?>,
                role: '<?php echo $_SESSION['role']; ?>'
            }
        };
    </script>
    <script type="module" src="/js/app.js"></script>
</body>
</html>