#!/usr/bin/env python3.5

import unittest

import git_importer

class TestProcessCommitMessage(unittest.TestCase):
    def test_empty_message(self):
        expected_result = {}
        self.assertEqual(git_importer.process_commit_message(
            '',
        ), expected_result)
        self.assertEqual(git_importer.process_commit_message(
            '  \n',
        ), expected_result)

    def test_merge_message(self):
        expected_result = {}
        self.assertEqual(git_importer.process_commit_message(
            "Merge branch 'development'\n",
        ), expected_result)
        self.assertEqual(git_importer.process_commit_message(
            "Merge branch 'issue-23' into development\n",
        ), expected_result)
        self.assertEqual(git_importer.process_commit_message(
            "Merge the branch 'issue-23' into the branch 'development'\n",
        ), expected_result)
        self.assertEqual(git_importer.process_commit_message(
            "  Merge branch 'development'\n",
        ), expected_result)
        self.assertEqual(git_importer.process_commit_message(
            "  Merge branch 'issue-23' into development\n",
        ), expected_result)
        self.assertEqual(git_importer.process_commit_message(
            "  Merge the branch 'issue-23' into the branch 'development'\n",
        ), expected_result)

    def test_message_without_issue_mark(self):
        expected_result = {'прочее': ['update the change log']}
        self.assertEqual(git_importer.process_commit_message(
            'Update the change log\n',
        ), expected_result)
        self.assertEqual(git_importer.process_commit_message(
            '  Update the change log\n',
        ), expected_result)

    def test_message_with_one_issue_mark(self):
        expected_result = {'issue #12': ['add the FizzBuzz class']}
        self.assertEqual(dict(git_importer.process_commit_message(
            'Issue #12: add the FizzBuzz class\n',
        )), expected_result)
        self.assertEqual(dict(git_importer.process_commit_message(
            '  Issue #12: add the FizzBuzz class\n',
        )), expected_result)

    def test_message_with_some_issues_marks(self):
        expected_result = {
            'issue #5': ['add the FizzBuzz class'],
            'issue #12': ['add the FizzBuzz class'],
        }
        self.assertEqual(dict(git_importer.process_commit_message(
            'Issue #5, issue #12: add the FizzBuzz class\n',
        )), expected_result)
        self.assertEqual(dict(git_importer.process_commit_message(
            '  Issue #5, issue #12: add the FizzBuzz class\n',
        )), expected_result)

if __name__ == '__main__':
    unittest.main()
