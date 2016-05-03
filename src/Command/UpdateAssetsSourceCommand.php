<?php

namespace CraftCli\Command;

use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;
use Craft\Craft;

class UpdateAssetsSourceCommand extends Command
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'update:assets_source';

    /**
     * {@inheritdoc}
     */
    protected $description = 'Update an Assets source.';

    /**
     * {@inheritdoc}
     */
    protected function getOptions()
    {
        return array(
            array(
                'name',
                null,
                InputOption::VALUE_REQUIRED,
                'Set the name of the Assets source.',
            ),
            array(
                'handle',
                null,
                InputOption::VALUE_REQUIRED,
                'Set the handle of the Assets source.',
            ),
            array(
                'type',
                null,
                InputOption::VALUE_REQUIRED,
                'Set the type of the Assets source (Local, S3, GoogleCloud, or Rackspace).',
            ),
            array(
                'public',
                null,
                InputOption::VALUE_NONE,
                'Set the Assets source to have public URLs.',
            ),
            array(
                'private',
                null,
                InputOption::VALUE_NONE,
                'Set the Assets source to NOT have public URLs.',
            ),
            array(
                'path',
                null,
                InputOption::VALUE_REQUIRED,
                'Set the file system path of a Local Assets source.',
            ),
            array(
                'url',
                null,
                InputOption::VALUE_REQUIRED,
                'Set the url of a Local Assets source.',
            ),
            array(
                'access_key',
                null,
                InputOption::VALUE_REQUIRED,
                'Set the Access Key ID of an S3/Google Cloud Assets source.',
            ),
            array(
                'secret_key',
                null,
                InputOption::VALUE_REQUIRED,
                'Set the Secret Access Key of an S3/Google Cloud Assets source.',
            ),
            array(
                'bucket',
                null,
                InputOption::VALUE_REQUIRED,
                'Set the Bucket of an S3/Google Cloud Assets source.',
            ),
            array(
                'subfolder',
                null,
                InputOption::VALUE_REQUIRED,
                'Set the subfolder of an S3/Google Cloud/Rackspace Cloud Assets source.',
            ),
            array(
                'url_prefix',
                null,
                InputOption::VALUE_REQUIRED,
                'Set the URL Prefix of an S3/Google Cloud/Rackspace Cloud Assets source.',
            ),
            array(
                'cache_duration',
                null,
                InputOption::VALUE_REQUIRED,
                'Set the Cache Duration of an S3/Google Cloud Assets source, ex. 10seconds.',
            ),
            array(
                'region',
                null,
                InputOption::VALUE_REQUIRED,
                'Set the Region of an S3/Rackspace Cloud Assets source.',
            ),
            array(
                'rackspace_username',
                null,
                InputOption::VALUE_REQUIRED,
                'Set the Username of a Rackspace Cloud Assets source.',
            ),
            array(
                'rackspace_api_key',
                null,
                InputOption::VALUE_REQUIRED,
                'Set the API Key of a Rackspace Cloud Assets source.',
            ),
            array(
                'rackspace_container',
                null,
                InputOption::VALUE_REQUIRED,
                'Set the Container of a Rackspace Cloud Assets source.',
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function getArguments()
    {
        return array(
            array(
                'source_id',
                InputArgument::REQUIRED,
                'ID of the Assets source to update',
            ),
        );
    }

    /**
     * {@inheritdoc}
     */
    protected function fire()
    {
        $source = craft()->assetSources->getSourceById($this->argument('source_id'));

        if ($this->option('name')) {
            $source->name = $this->option('name');
        }

        if ($this->option('handle')) {
            $source->handle = $this->option('handle');
        }

        if (craft()->getEdition() === Craft::Pro && $this->option('type')) {
            $validTypes = array('Local', 'S3', 'GoogleCloud', 'Rackspace');

            if (! in_array($this->option('type'), $validTypes)) {
                $this->error('Type ust be one of: Local, S3, GoogleCloud, or Rackspace');

                return;
            }

            $source->type = $this->option('type');
        }

        $settings = $source->settings;

        if ($this->option('private')) {
            $settings['publicURLs'] = false;
        } elseif ($this->option('public')) {
            $settings['publicURLs'] = true;
        }

        switch ($source->type) {
            case 'Local':
                if ($this->option('path')) {
                    $settings['path'] = $this->option('path');
                }

                if ($this->option('url')) {
                    $settings['url'] = $this->option('url');
                }

                break;
            case 'S3':
                if ($this->option('access_key')) {
                    $settings['keyId'] = $this->option('access_key');
                }

                if ($this->option('secret_key')) {
                    $settings['secret'] = $this->option('secret_key');
                }

                if ($this->option('bucket')) {
                    $settings['bucket'] = $this->option('bucket');
                }

                if ($this->option('subfolder')) {
                    $settings['subfolder'] = $this->option('subfolder');
                }

                if ($this->option('url_prefix')) {
                    $settings['urlPrefix'] = $this->option('url_prefix');
                }

                if ($this->option('region')) {
                    $settings['location'] = $this->option('region');
                }

                break;
            case 'GoogleCloud':
                if ($this->option('access_key')) {
                    $settings['keyId'] = $this->option('access_key');
                }

                if ($this->option('secret_key')) {
                    $settings['secret'] = $this->option('secret_key');
                }

                if ($this->option('bucket')) {
                    $settings['bucket'] = $this->option('bucket');
                }

                if ($this->option('subfolder')) {
                    $settings['subfolder'] = $this->option('subfolder');
                }

                if ($this->option('url_prefix')) {
                    $settings['urlPrefix'] = $this->option('url_prefix');
                }

                break;
            case 'Rackspace':
                if ($this->option('rackspace_username')) {
                    $settings['username'] = $this->option('rackspace_username');
                }

                if ($this->option('rackspace_api_key')) {
                    $settings['apiKey'] = $this->option('rackspace_api_key');
                }

                if ($this->option('region')) {
                    $settings['region'] = $this->option('region');
                }

                if ($this->option('container')) {
                    $settings['container'] = $this->option('container');
                }

                if ($this->option('subfolder')) {
                    $settings['subfolder'] = $this->option('subfolder');
                }

                if ($this->option('url_prefix')) {
                    $settings['urlPrefix'] = $this->option('url_prefix');
                }

                break;
        }

        $source->settings = $settings;

        if (craft()->assetSources->saveSource($source)) {
            $this->info('Source saved.');
        } else {
            foreach ($source->getAllErrors() as $error) {
                $this->error($error);
            }
        }
    }
}
