import setuptools

packages = setuptools.find_packages()
package_name = packages[0]
project_name = package_name.replace('_', '-')
setuptools.setup(
    name=project_name,
    license='MIT',
    author='thewizardplusplus',
    author_email='thewizardplusplus@yandex.ru',
    url='https://github.com/thewizardplusplus/wizard-diary',
    packages=packages,
    install_requires=[
        'xerox >=0.4.1, <1.0',
        'termcolor >=2.4.0, <3.0.0',
        'dataclasses_json >=0.5.7, <0.6.0',
    ],
    python_requires='>=3.5, <4.0',
    entry_points={'console_scripts': [
        '{} = {}:main'.format(project_name, package_name),
    ]},
)
