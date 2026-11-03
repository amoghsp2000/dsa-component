#!/usr/bin/env python3
"""
Test runner script for Python DSA Toolbox test suite.
"""
import unittest
import sys
from pathlib import Path

ROOT_DIR = Path(__file__).resolve().parent.parent
TESTS_DIR = ROOT_DIR / "tests" / "python"

if __name__ == "__main__":
    suite = unittest.defaultTestLoader.discover(str(TESTS_DIR), pattern="test_*.py")
    runner = unittest.TextTestRunner(verbosity=2)
    result = runner.run(suite)
    sys.exit(0 if result.wasSuccessful() else 1)

