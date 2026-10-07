export default {
    // Signed in = the API token and the user's details are both stored (see loginJWT)
    isUserLoggedIn: () => !!(localStorage.getItem('accessToken') && localStorage.getItem('userInfo')),
}
