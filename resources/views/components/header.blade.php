@php
    $isLoggedIn = Session::has('user_id');
    $userName = Session::get('user_name', '');
@endphp

<header style="position: relative; background: #0f0f15; border-bottom: 1px solid #1e1e2a; top: 0; z-index: 9999; backdrop-filter: blur(10px); width: 100%; overflow: visible;">
        <div class="container" style="max-width: 1280px; margin: 0 auto; padding: 0 20px; width: 100%; overflow: visible;">
        <div style="display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 15px; padding: 16px 0; overflow: visible;">
            <!-- Logo -->
            <a href="{{ url('/') }}" style="display: flex; align-items: center; gap: 10px; text-decoration: none;">
                <div style="width: 40px; height: 40px; background: linear-gradient(135deg, #3b82f6, #8b5cf6); border-radius: 12px; display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-mobile-alt" style="color: white; font-size: 20px;"></i>
                </div>
                <h1 style="font-size: 24px; font-weight: 800; background: linear-gradient(135deg, #60a5fa, #a78bfa); -webkit-background-clip: text; background-clip: text; color: transparent;">RevoDevice</h1>
            </a>

            <!-- Search Bar -->
            <div style="flex: 1; max-width: 400px; min-width: 200px; position: relative;">
                <i class="fas fa-search" style="position: absolute; left: 18px; top: 50%; transform: translateY(-50%); color: #64748b;"></i>
                <input type="text" placeholder="Search devices for buy or sell" 
                       style="width: 100%; padding: 12px 20px 12px 45px; background: #1a1a2e; border: 1px solid #2a2a3a; border-radius: 30px; color: white; outline: none;">
            </div>

            <!-- Location & User -->
            <div style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
                <div style="display: flex; align-items: center; gap: 8px; background: #1a1a2e; padding: 8px 16px; border-radius: 30px; cursor: pointer;">
                    <i class="fas fa-map-marker-alt" style="color: #3b82f6;"></i>
                    <span style="font-size: 14px;">New Delhi</span>
                    <i class="fas fa-chevron-down" style="font-size: 10px;"></i>
                </div>
                
                @if($isLoggedIn)
                    <!-- User Dropdown when logged in -->
                    <div class="user-dropdown" style="position: relative; z-index: 10000;">
                        <button class="user-btn" style="background: linear-gradient(135deg, #3b82f6, #8b5cf6); color: white; padding: 8px 20px; border-radius: 30px; border: none; font-weight: 600; cursor: pointer; display: flex; align-items: center; gap: 8px;">
                            <i class="fas fa-user-circle"></i>
                            <span id="userNameSpan">{{ $userName }}</span>
                            <i class="fas fa-chevron-down" style="font-size: 10px;"></i>
                        </button>
                        <div class="user-dropdown-content" style="position: absolute; top: 100%; right: 0; background: #1a1a2e; min-width: 180px; border-radius: 12px; padding: 8px 0; opacity: 0; visibility: hidden; transition: all 0.2s; border: 1px solid #2a2a3a; z-index: 10001; box-shadow: 0 10px 25px -5px rgba(0,0,0,0.3);">
                            <a href="#" id="profileLink" style="display: block; padding: 10px 20px; color: #cbd5e1; text-decoration: none; font-size: 13px; cursor: pointer;">
                                <i class="fas fa-user" style="margin-right: 8px;"></i> My Profile
                            </a>
                            <a href="#" id="logoutBtn" style="display: block; padding: 10px 20px; color: #cbd5e1; text-decoration: none; font-size: 13px; cursor: pointer;">
                                <i class="fas fa-sign-out-alt" style="margin-right: 8px;"></i> Logout
                            </a>
                        </div>
                    </div>
                @else
                    <!-- Login Button -->
                    <button id="loginBtn" style="background: linear-gradient(135deg, #3b82f6, #8b5cf6); color: white; padding: 8px 20px; border-radius: 30px; border: none; font-weight: 600; cursor: pointer;">
                        Login
                    </button>
                @endif
            </div>
        </div>
    </div>
</header>

<!-- Login Popup Modal (Same as before) -->
<div id="loginPopup" class="login-popup" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.8); z-index: 99999; justify-content: center; align-items: center;">
    <!-- Popup content remains same -->
    <div style="background: #111118; border-radius: 24px; padding: 40px; max-width: 400px; width: 90%; border: 1px solid #2a2a3a; position: relative; z-index: 100000;">
        <button id="closePopup" style="position: absolute; top: 15px; right: 20px; background: none; border: none; color: #94a3b8; font-size: 24px; cursor: pointer;">&times;</button>
        
        <div id="phoneStep" style="text-align: center;">
            <i class="fas fa-mobile-alt" style="font-size: 50px; color: #3b82f6; margin-bottom: 20px;"></i>
            <h3 style="color: white; margin-bottom: 10px;">Login / Signup</h3>
            <p style="color: #94a3b8; margin-bottom: 25px; font-size: 14px;">Enter your mobile number to continue</p>
            
            <input type="tel" id="mobileNumber" maxlength="10" placeholder="Enter 10-digit mobile number" 
                   style="width: 100%; padding: 14px; background: #1a1a2e; border: 1px solid #2a2a3a; border-radius: 12px; color: white; font-size: 16px; margin-bottom: 20px; text-align: center;">
            
            <div id="mobileError" style="color: #ef4444; font-size: 12px; margin-bottom: 15px; display: none;"></div>
            
            <button id="sendOtpBtn" style="width: 100%; padding: 14px; background: linear-gradient(135deg, #3b82f6, #8b5cf6); color: white; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; font-size: 16px;">
                Send OTP
            </button>
        </div>
        
        <div id="otpStep" style="display: none; text-align: center;">
            <i class="fas fa-key" style="font-size: 50px; color: #3b82f6; margin-bottom: 20px;"></i>
            <h3 style="color: white; margin-bottom: 10px;">Enter OTP</h3>
            <p style="color: #94a3b8; margin-bottom: 25px; font-size: 14px;">We've sent a 4-digit code to <span id="displayPhone"></span></p>
            
            <div style="display: flex; gap: 10px; justify-content: center; margin-bottom: 20px;">
                <input type="text" id="otp1" maxlength="1" class="otp-input" style="width: 50px; height: 50px; text-align: center; font-size: 20px; background: #1a1a2e; border: 1px solid #2a2a3a; border-radius: 12px; color: white;">
                <input type="text" id="otp2" maxlength="1" class="otp-input" style="width: 50px; height: 50px; text-align: center; font-size: 20px; background: #1a1a2e; border: 1px solid #2a2a3a; border-radius: 12px; color: white;">
                <input type="text" id="otp3" maxlength="1" class="otp-input" style="width: 50px; height: 50px; text-align: center; font-size: 20px; background: #1a1a2e; border: 1px solid #2a2a3a; border-radius: 12px; color: white;">
                <input type="text" id="otp4" maxlength="1" class="otp-input" style="width: 50px; height: 50px; text-align: center; font-size: 20px; background: #1a1a2e; border: 1px solid #2a2a3a; border-radius: 12px; color: white;">
            </div>
            
            <div id="otpError" style="color: #ef4444; font-size: 12px; margin-bottom: 15px; display: none;"></div>
            
            <button id="verifyOtpBtn" style="width: 100%; padding: 14px; background: linear-gradient(135deg, #3b82f6, #8b5cf6); color: white; border: none; border-radius: 12px; font-weight: 600; cursor: pointer; font-size: 16px;">
                Verify OTP
            </button>
            
            <button id="backToPhone" style="background: none; border: none; color: #3b82f6; margin-top: 15px; cursor: pointer; font-size: 14px;">
                ← Back to mobile number
            </button>
        </div>
    </div>
</div>

<style>
    .user-dropdown {
        position: relative;
        z-index: 10000;
    }
    
    .user-dropdown:hover .user-dropdown-content {
        opacity: 1 !important;
        visibility: visible !important;
    }
    
    .user-dropdown-content a:hover {
        background: #2a2a3a;
    }
    
    .otp-input:focus {
        outline: none;
        border-color: #3b82f6;
    }
    
    @media (max-width: 768px) {
        header .container > div {
            flex-direction: column;
            text-align: center;
        }
        header .search-bar {
            max-width: 100%;
            width: 100%;
        }
        header .location-login {
            justify-content: center;
        }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loginBtn = document.getElementById('loginBtn');
    const loginPopup = document.getElementById('loginPopup');
    const closePopup = document.getElementById('closePopup');
    const phoneStep = document.getElementById('phoneStep');
    const otpStep = document.getElementById('otpStep');
    const sendOtpBtn = document.getElementById('sendOtpBtn');
    const verifyOtpBtn = document.getElementById('verifyOtpBtn');
    const mobileNumber = document.getElementById('mobileNumber');
    const displayPhone = document.getElementById('displayPhone');
    const mobileError = document.getElementById('mobileError');
    const otpError = document.getElementById('otpError');
    const backToPhone = document.getElementById('backToPhone');
    const logoutBtn = document.getElementById('logoutBtn');
    
    let storedPhone = '';
    
    // Open popup
    if (loginBtn) {
        loginBtn.addEventListener('click', function() {
            loginPopup.style.display = 'flex';
            phoneStep.style.display = 'block';
            otpStep.style.display = 'none';
            mobileNumber.value = '';
            clearOtpInputs();
        });
    }
    
    // Close popup
    if (closePopup) {
        closePopup.addEventListener('click', function() {
            loginPopup.style.display = 'none';
        });
    }
    
    // Close popup when clicking outside
    if (loginPopup) {
        loginPopup.addEventListener('click', function(e) {
            if (e.target === loginPopup) {
                loginPopup.style.display = 'none';
            }
        });
    }
    
    // Send OTP
    if (sendOtpBtn) {
        sendOtpBtn.addEventListener('click', async function() {
            const phone = mobileNumber.value.trim();
            
            if (!phone || phone.length !== 10) {
                mobileError.textContent = 'Please enter a valid 10-digit mobile number';
                mobileError.style.display = 'block';
                return;
            }
            
            mobileError.style.display = 'none';
            sendOtpBtn.disabled = true;
            sendOtpBtn.textContent = 'Sending...';
            
            try {
                const response = await fetch('{{ route("send.otp") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ mob_no: phone })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    storedPhone = phone;
                    displayPhone.textContent = phone;
                    phoneStep.style.display = 'none';
                    otpStep.style.display = 'block';
                    clearOtpInputs();
                    
                    // Auto-fill OTP in development (remove in production)
                    if (data.otp) {
                        const otpStr = data.otp.toString();
                        document.getElementById('otp1').value = otpStr[0] || '';
                        document.getElementById('otp2').value = otpStr[1] || '';
                        document.getElementById('otp3').value = otpStr[2] || '';
                        document.getElementById('otp4').value = otpStr[3] || '';
                    }
                } else {
                    mobileError.textContent = data.message || 'Failed to send OTP';
                    mobileError.style.display = 'block';
                }
            } catch (error) {
                mobileError.textContent = 'Network error. Please try again.';
                mobileError.style.display = 'block';
            } finally {
                sendOtpBtn.disabled = false;
                sendOtpBtn.textContent = 'Send OTP';
            }
        });
    }
    
    // Verify OTP
    if (verifyOtpBtn) {
        verifyOtpBtn.addEventListener('click', async function() {
            const otp = getOtpValue();
            
            if (otp.length !== 4) {
                otpError.textContent = 'Please enter complete 4-digit OTP';
                otpError.style.display = 'block';
                return;
            }
            
            otpError.style.display = 'none';
            verifyOtpBtn.disabled = true;
            verifyOtpBtn.textContent = 'Verifying...';
            
            try {
                const response = await fetch('{{ route("verify.otp") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ phone: storedPhone, otp: otp })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    loginPopup.style.display = 'none';
                    window.location.reload();
                } else {
                    otpError.textContent = data.message || 'Invalid OTP';
                    otpError.style.display = 'block';
                }
            } catch (error) {
                otpError.textContent = 'Network error. Please try again.';
                otpError.style.display = 'block';
            } finally {
                verifyOtpBtn.disabled = false;
                verifyOtpBtn.textContent = 'Verify OTP';
            }
        });
    }
    
    // Back to phone step
    if (backToPhone) {
        backToPhone.addEventListener('click', function() {
            phoneStep.style.display = 'block';
            otpStep.style.display = 'none';
            mobileNumber.value = storedPhone;
        });
    }
    
    // OTP input auto-tab
    const otpInputs = document.querySelectorAll('.otp-input');
    otpInputs.forEach((input, index) => {
        input.addEventListener('input', function(e) {
            if (this.value.length === 1 && index < 3) {
                otpInputs[index + 1].focus();
            }
        });
        
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace' && !this.value && index > 0) {
                otpInputs[index - 1].focus();
            }
        });
    });
    
    // Logout
    if (logoutBtn) {
        logoutBtn.addEventListener('click', async function(e) {
            e.preventDefault();
            
            try {
                const response = await fetch('{{ route("logout") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    // Force immediate page reload to update UI
                    window.location.href = data.redirect || '/';
                }
            } catch (error) {
                console.error('Logout error:', error);
                // Force reload on error
                window.location.href = '/';
            }
        });
    }
    
    function getOtpValue() {
        return document.getElementById('otp1').value +
               document.getElementById('otp2').value +
               document.getElementById('otp3').value +
               document.getElementById('otp4').value;
    }
    
    function clearOtpInputs() {
        document.getElementById('otp1').value = '';
        document.getElementById('otp2').value = '';
        document.getElementById('otp3').value = '';
        document.getElementById('otp4').value = '';
        if (document.getElementById('otp1')) {
            document.getElementById('otp1').focus();
        }
    }
});
</script>