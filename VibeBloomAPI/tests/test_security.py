from hashlib import sha256
import unittest

from app.core.security import (
    hash_password,
    password_needs_rehash,
    verify_password,
)


class PasswordSecurityTests(unittest.TestCase):
    def test_same_password_gets_different_salted_hashes(self):
        first = hash_password("UnaPasswordSegura123")
        second = hash_password("UnaPasswordSegura123")

        self.assertNotEqual(first, second)
        self.assertTrue(first.startswith("$2b$"))
        self.assertTrue(verify_password("UnaPasswordSegura123", first))
        self.assertTrue(verify_password("UnaPasswordSegura123", second))

    def test_wrong_password_is_rejected(self):
        password_hash = hash_password("UnaPasswordSegura123")

        self.assertFalse(verify_password("otra-password", password_hash))

    def test_legacy_sha256_remains_valid_and_requires_rehash(self):
        legacy_hash = sha256("password-anterior".encode("utf-8")).hexdigest()

        self.assertTrue(verify_password("password-anterior", legacy_hash))
        self.assertTrue(password_needs_rehash(legacy_hash))
        self.assertFalse(password_needs_rehash(hash_password("password-anterior")))


if __name__ == "__main__":
    unittest.main()
