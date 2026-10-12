import { isSignedIn } from '@/http/requests/auth/session.js'

export default {
    // Signed in = the API token and the user's details are both stored (see loginJWT), and the token is still valid
    isUserLoggedIn: isSignedIn,
}
