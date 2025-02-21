<div class="modal fade" id="sectorAdminModal" tabindex="-1" aria-labelledby="sectorAdminModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-light">
                <h5 class="modal-title" id="sectorAdminModalLabel">
                    <i class="fas fa-user-shield me-2"></i>Sektor Admini Təyin Et
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Bağla"></button>
            </div>

            <form id="sectorAdminForm" method="POST" class="needs-validation" novalidate>
                @csrf
                <input type="hidden" name="user_type" value="sectoradmin">
                <input type="hidden" id="sectorIdInput" name="sector_id">
                <!-- Add hidden sector_id field -->
                <input type="hidden" name="sector_id" value="{{ $sector->id }}">

                <!-- Ümumi xəta mesajları -->
                <div id="formErrors" class="alert alert-danger d-none mx-3 mt-3"></div>

                <div class="modal-body">
                    <!-- Şəxsi məlumatlar -->
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="first_name" class="form-control" id="firstNameInput" 
                                       placeholder="Adınız" required>
                                <label for="firstNameInput">Ad</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="last_name" class="form-control" id="lastNameInput" 
                                       placeholder="Soyadınız" required>
                                <label for="lastNameInput">Soyad</label>
                            </div>
                        </div>
                    </div>

                    <!-- Əlaqə məlumatları -->
                    <div class="row g-3 mt-2">
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="email" name="email" class="form-control" id="emailInput" 
                                       placeholder="nümunə@domain.az" required>
                                <label for="emailInput">Email</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-floating">
                                <input type="text" name="utis_code" class="form-control" id="utisCodeInput" 
                                       placeholder="1234567" required maxlength="7" pattern="[0-9]{7}">
                                <label for="utisCodeInput">UTIS Kodu</label>
                                <small class="form-text text-muted">7 rəqəmli UTIS kodunuzu daxil edin</small>
                            </div>
                        </div>
                    </div>

                    <!-- Hesab məlumatları -->
                    <div class="row g-3 mt-2">
                        <div class="col-md-12">
                            <div class="form-floating">
                                <input type="text" name="username" class="form-control" id="usernameInput" 
                                       placeholder="istifadəçi_adı" required>
                                <label for="usernameInput">İstifadəçi adı</label>
                                <small class="form-text text-muted">Hərf, rəqəm və alt xətt (_) istifadə edə bilərsiniz</small>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-floating">
                                <input type="password" name="password" class="form-control" id="passwordInput" 
                                       placeholder="şifrə" required>
                                <label for="passwordInput">Şifrə</label>
                                <div class="password-requirements small text-muted mt-1">
                                    <div><i class="fas fa-info-circle"></i> Minimum 8 simvol</div>
                                    <div><i class="fas fa-info-circle"></i> Ən az bir böyük hərf</div>
                                    <div><i class="fas fa-info-circle"></i> Ən az bir kiçik hərf</div>
                                    <div><i class="fas fa-info-circle"></i> Ən az bir rəqəm</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-1"></i>Bağla
                    </button>
                    <button type="submit" id="sectorAdminSubmitBtn" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Təyin et
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
