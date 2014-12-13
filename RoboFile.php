<?php

/**
 * Define Robo commands for building and testing Drupal User Registry Codeception module.
 *
 * @see http://robo.li/
 */
class RoboFile extends \Robo\Tasks
{
    /**
     * @type string
     *   Location of directory containing source code.
     */
    const SRC_DIR = "src";

    /**
     * @type string
     *   Location of directory containing documentation files.
     */
    const DOCS_DIR = "docs";

    /**
     * @type string
     *   Location of directory containing Codeception tests.
     */
    const TESTS_DIR = "tests";

    /**
     * @type string
     *   Path to Codeception.
     */
    const CODECEPTION_PATH = "vendor/bin/codecept";

    /**
     * Run PHP_CodeSniffer standards checks.
     */
    public function cleanupPhpcs()
    {
        $directories = [self::SRC_DIR, self::TESTS_DIR];

        foreach ($directories as $directory) {
            $this->taskExec("phpcs --standard=PSR2 " . $directory)
                ->printed(false)
                ->run();
        }
    }

    /**
     * Generate PhpDocumentator files.
     */
    public function docsGenerate()
    {
        $this->taskExec(sprintf("phpdoc run -d %s -t %s", self::SRC_DIR, self::DOCS_DIR))
            ->run();
    }

    /**
     * Clean PhpDocumentor directory.
     */
    public function docsClean()
    {
        $this->taskCleanDir(self::DOCS_DIR)->run();
    }

    /**
     * Build Codeception's auto-generated files.
     */
    public function testsBuild()
    {
        $this->taskExec(sprintf("%s build", self::CODECEPTION_PATH))->run();
    }

    /**
     * Run (functional) tests.
     */
    public function testsRun()
    {
        // Still have to build the *Tester classes if we've just checked out.
        $this->testsBuild();

        foreach (["unit", "acceptance"] as $suite) {
            $this->taskCodecept(self::CODECEPTION_PATH)
                ->option("config", "codeception.yml")
                ->suite($suite)
                ->run();
        }
    }

    /**
     * Run CodeCoverage.
     */
    public function testsCoverage()
    {
        // Unable to use taskCodecept() here as "Executing vendor/bin/codecept run --coverage-html unit" actually runs
        // the acceptance suite too. The syntax below (with suite before options) seems to work:
        //
//        $this->taskCodecept(self::CODECEPTION_PATH)
//            ->suite("unit")
//            ->option("coverage-html")
//            ->run();
        // @todo Investigate and submit issue.
        $this->taskExec(self::CODECEPTION_PATH . " run unit --coverage-html")->run();
    }
}
