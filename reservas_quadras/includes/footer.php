        </div> <!-- Fecha container -->
    </main>

    <footer class="footer">
        <div class="container">
            <div class="footer-content">
                <div class="footer-section">
                    <h3><i class="fas fa-volleyball-ball"></i> LestoPlay Arenas</h3>
                    <p>O melhor sistema de reservas de quadras de areia da região. Faça sua reserva de forma rápida e segura.</p>
                </div>
                <div class="footer-section">
                    <h4>Contato</h4>
                    <p><i class="fas fa-phone"></i> (11) 9999-9999</p>
                    <p><i class="fas fa-envelope"></i> contato@arenasports.com</p>
                    <p><i class="fas fa-map-marker-alt"></i> Rua das Quadras, 123 - São Paulo/SP</p>
                </div>
                <div class="footer-section">
                    <h4>Horário de Funcionamento</h4>
                    <p><i class="fas fa-clock"></i> Segunda a Sexta: 06h às 23h</p>
                    <p><i class="fas fa-clock"></i> Sábado e Domingo: 07h às 22h</p>
                </div>
                <div class="footer-section">
                    <h4>Redes Sociais</h4>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-whatsapp"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date('Y'); ?> LestoPlay Arenas. Todos os direitos reservados.</p>
            </div>
        </div>
    </footer>

    <script>
        // Menu mobile
        document.querySelector('.mobile-menu-toggle').addEventListener('click', function() {
            document.querySelector('.nav').classList.toggle('active');
        });

        // Fechar alertas
        document.querySelectorAll('.alert').forEach(function(alert) {
            setTimeout(function() {
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 300);
            }, 5000);
        });

        // Confirmação para cancelar reservas
        document.querySelectorAll('.btn-cancelar').forEach(function(btn) {
            btn.addEventListener('click', function(e) {
                if (!confirm('Tem certeza que deseja cancelar esta reserva?')) {
                    e.preventDefault();
                }
            });
        });

        // Validação de formulários
        document.querySelectorAll('form').forEach(function(form) {
            form.addEventListener('submit', function(e) {
                const requiredFields = form.querySelectorAll('[required]');
                let valid = true;

                requiredFields.forEach(function(field) {
                    if (!field.value.trim()) {
                        field.classList.add('error');
                        valid = false;
                    } else {
                        field.classList.remove('error');
                    }
                });

                if (!valid) {
                    e.preventDefault();
                    alert('Por favor, preencha todos os campos obrigatórios.');
                }
            });
        });
    </script>
</body>
</html>

