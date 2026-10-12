// The signed-in session kept in this browser: the API token, the user, and when the token expires
// (every sign-in lasts auth.token_lifetime.days on the server, 3 months by default).
const EXPIRES_KEY = 'tokenExpiresAt'

// Keep when the token ends, from the sign-in answer ("2027-01-10 13:47:47", server time)
export function saveSessionExpiry(expiresAt) {
    try {
        if (expiresAt) localStorage.setItem(EXPIRES_KEY, expiresAt)
        else localStorage.removeItem(EXPIRES_KEY)
    } catch (e) { /* storage unavailable */ }
}

// Has the token's time run out? (No date stored: the API decides, answering 401 when it has.)
export function sessionExpired() {
    const at = Date.parse(String(localStorage.getItem(EXPIRES_KEY) || '').replace(' ', 'T'))
    return Number.isFinite(at) && Date.now() >= at
}

export function hasSession() {
    return Boolean(localStorage.getItem('accessToken') && localStorage.getItem('userInfo'))
}

// Signed in = token and user stored, and the token still valid
export function isSignedIn() {
    return hasSession() && !sessionExpired()
}

// Forget the session in this browser (sign-out, expired or revoked token)
export function clearSession() {
    ['accessToken', 'userInfo', 'role_menus', EXPIRES_KEY].forEach(key => localStorage.removeItem(key))
}
