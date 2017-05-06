#!/usr/bin/env python3.5

import unittest
import datetime

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
        self.assertEqual(git_importer.process_commit_message(
            'Issue #12: add the FizzBuzz class\n',
        ), expected_result)
        self.assertEqual(git_importer.process_commit_message(
            '  Issue #12: add the FizzBuzz class\n',
        ), expected_result)

    def test_message_with_some_issues_marks(self):
        expected_result = {
            'issue #5': ['add the FizzBuzz class'],
            'issue #12': ['add the FizzBuzz class'],
        }
        self.assertEqual(git_importer.process_commit_message(
            'Issue #5, issue #12: add the FizzBuzz class\n',
        ), expected_result)
        self.assertEqual(git_importer.process_commit_message(
            '  Issue #5, issue #12: add the FizzBuzz class\n',
        ), expected_result)

class TestProcessGitHistory(unittest.TestCase):
    def test_empty_commit_list(self):
        self.assertEqual(git_importer.process_git_history([]), {})

    def test_unique_commits(self):
        timestamp_1 = datetime.datetime(2017, 5, 5)
        timestamp_2 = datetime.datetime(2017, 5, 12)
        self.assertEqual(git_importer.process_git_history([
            git_importer.Commit(
                timestamp_1,
                'Issue #5: add the FizzBuzz class',
            ),
            git_importer.Commit(
                timestamp_2,
                'Issue #12: add the LinkedList class',
            ),
        ]), {
            timestamp_1.date(): {'issue #5': ['add the FizzBuzz class']},
            timestamp_2.date(): {'issue #12': ['add the LinkedList class']},
        })

    def test_commits_with_same_timestamps(self):
        timestamp_1 = datetime.datetime(2017, 5, 5)
        timestamp_2 = datetime.datetime(2017, 5, 12, 2, 4, 6)
        timestamp_3 = datetime.datetime(2017, 5, 12, 12, 34, 56)
        self.assertEqual(git_importer.process_git_history([
            git_importer.Commit(
                timestamp_1,
                'Issue #5: add the FizzBuzz class',
            ),
            git_importer.Commit(
                timestamp_1,
                'Issue #12: add the LinkedList class',
            ),
            git_importer.Commit(
                timestamp_2,
                'Issue #5: add the FizzBuzz class',
            ),
            git_importer.Commit(
                timestamp_3,
                'Issue #12: add the LinkedList class',
            ),
        ]), {
            timestamp_1.date(): {
                'issue #5': ['add the FizzBuzz class'],
                'issue #12': ['add the LinkedList class'],
            },
            timestamp_2.date(): {
                'issue #5': ['add the FizzBuzz class'],
                'issue #12': ['add the LinkedList class'],
            },
        })

    def test_commits_with_same_issues_marks(self):
        timestamp = datetime.datetime(2017, 5, 5)
        self.assertEqual(git_importer.process_git_history([
            git_importer.Commit(timestamp, 'Issue #5: add the FizzBuzz class'),
            git_importer.Commit(
                timestamp,
                'Issue #5: add the LinkedList class',
            ),
        ]), {timestamp.date(): {'issue #5': [
            'add the FizzBuzz class',
            'add the LinkedList class',
        ]}})

    def test_commits_with_some_issues_marks(self):
        timestamp = datetime.datetime(2017, 5, 5)
        self.assertEqual(git_importer.process_git_history([
            git_importer.Commit(
                timestamp,
                'Issue #5, issue #12: add the FizzBuzz class',
            ),
            git_importer.Commit(
                timestamp,
                'Issue #5, issue #12: add the LinkedList class',
            ),
        ]), {timestamp.date(): {
            'issue #5': [
                'add the FizzBuzz class',
                'add the LinkedList class',
            ],
            'issue #12': [
                'add the FizzBuzz class',
                'add the LinkedList class',
            ],
        }})

if __name__ == '__main__':
    unittest.main()
