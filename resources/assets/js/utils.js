/**
 * Uppercase first character of a string.
 * @returns {string}
 */
String.prototype.ucfirst = function() {
    return this.charAt(0).toUpperCase() + this.slice(1);
};