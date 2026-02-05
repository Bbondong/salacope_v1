<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Double Authentification - S'alacoop</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style/double_authen.css">
</head>
<body>
    <div class="container">
        <div class="auth-card">
            <!-- Logo -->
            <div class="logo">
                <div class="logo-icon">
                    <i class="fas fa-shield-alt"></i>
                </div>
                <h1>Salacope</h1>
                <p>Double authentification</p>
            </div>

            <!-- Étape -->
            <div class="step-indicator">
                <div class="step active"></div>
                <div class="step"></div>
                <div class="step"></div>
            </div>

            <!-- Titre -->
            <div class="title">
                <h2>Vérification en 2 étapes</h2>
                <p>Entrez le code à 6 chiffres envoyé sur WhatsApp</p>
            </div>

            <!-- Info utilisateur -->
            <div class="user-info" id="user-info">
                <div class="info-item">
                    <span class="info-label">Téléphone :</span>
                    <span class="info-value" id="user-phone">Chargement...</span>
                </div>
                <div class="info-item">
                    <span class="info-label">Code expiré dans :</span>
                    <span class="info-value" id="code-expiry">30:00</span>
                </div>
            </div>

            <!-- Champs code -->
            <div class="code-input-container">
                <label class="code-label">Code de vérification</label>
                <div class="code-inputs">
                    <input type="text" class="code-input" maxlength="1" data-index="0" inputmode="numeric" autocomplete="off">
                    <input type="text" class="code-input" maxlength="1" data-index="1" inputmode="numeric" autocomplete="off">
                    <input type="text" class="code-input" maxlength="1" data-index="2" inputmode="numeric" autocomplete="off">
                    <input type="text" class="code-input" maxlength="1" data-index="3" inputmode="numeric" autocomplete="off">
                    <input type="text" class="code-input" maxlength="1" data-index="4" inputmode="numeric" autocomplete="off">
                    <input type="text" class="code-input" maxlength="1" data-index="5" inputmode="numeric" autocomplete="off">
                </div>
                <div class="code-hint" id="code-hint">
                    <i class="fas fa-info-circle"></i>
                    <span>Code envoyé sur WhatsApp</span>
                </div>
            </div>

            <!-- Timer -->
            <div class="timer-container">
                <div class="timer" id="timer">
                    <i class="fas fa-clock"></i>
                    Code valide pendant : <span id="countdown">30:00</span>
                </div>
            </div>

            <!-- Actions -->
            <div class="actions">
                <button class="btn btn-primary" id="verify-btn">
                    <i class="fas fa-check-circle"></i>
                    <span>Vérifier le code</span>
                </button>
                
                <button class="btn btn-secondary" id="resend-btn" disabled>
                    <i class="fas fa-redo"></i>
                    <span>Renvoyer le code</span>
                    <span class="resend-timer" id="resend-timer">(60s)</span>
                </button>

                <button class="btn btn-whatsapp" id="whatsapp-btn">
                    <i class="fab fa-whatsapp"></i>
                    <span>Ouvrir WhatsApp</span>
                </button>

                <button class="btn btn-link" id="help-btn">
                    <i class="fas fa-question-circle"></i>
                    <span>Je n'ai pas reçu de code</span>
                </button>
            </div>

            <!-- État de l'envoi -->
            <div class="whatsapp-status" id="whatsapp-status">
                <div class="status-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="status-text">
                    <p>Code envoyé sur WhatsApp</p>
                    <small>Si vous ne l'avez pas reçu, cliquez sur "Ouvrir WhatsApp"</small>
                </div>
            </div>
        </div>

        <!-- Modal d'aide -->
        <div class="modal" id="help-modal">
            <div class="modal-content">
                <div class="modal-header">
                    <h3><i class="fas fa-question-circle"></i> Aide</h3>
                    <button class="modal-close" id="close-help-modal">&times;</button>
                </div>
                <div class="modal-body">
                    <div class="help-item">
                        <h4><i class="fas fa-mobile-alt"></i> Je n'ai pas reçu le code</h4>
                        <p>1. Vérifiez votre connexion Internet</p>
                        <p>2. Attendez 60 secondes pour renvoyer le code</p>
                        <p>3. Cliquez sur "Ouvrir WhatsApp" pour contacter le support</p>
                    </div>
                    <div class="help-item">
                        <h4><i class="fas fa-clock"></i> Le code a expiré</h4>
                        <p>Les codes de vérification expirent après 30 minutes pour des raisons de sécurité.</p>
                    </div>
                    <div class="help-item">
                        <h4><i class="fas fa-phone"></i> Contactez le support</h4>
                        <p><strong>WhatsApp :</strong> +243 962 763 130</p>
                        <p><strong>Email :</strong> support@salacope.com</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="../style/js/double_authen.js"></script>
</body>
</html>