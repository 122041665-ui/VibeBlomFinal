from app.services.content_moderator import basic_content_violation


def test_rejects_offensive_historical_review():
    assert basic_content_violation("puto") is not None


def test_accepts_useful_review():
    assert basic_content_violation(
        "Lugar accesible, tranquilo y con una vista muy bonita."
    ) is None
