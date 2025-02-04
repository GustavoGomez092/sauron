# Struck Logger WordPress Plugin (Formerly Sauron)

Struck Logger is a WordPress plugin designed to log user actions and generate comprehensive reports, enhancing your website's security and user activity monitoring.

## Features

- **User Action Logging**: Records various user activities within your WordPress site.
- **Comprehensive Reports**: Generates detailed reports to help you analyze user behavior.
- **Vue 3 Integration**: Built with Vue 3 for a modern and responsive user interface.
- **Tailwind CSS**: Styled using Tailwind CSS for a consistent and customizable design.

## Installation

1. **Clone the Repository**:

   ```bash
   git clone https://github.com/GustavoGomez092/struck.git
   ```

2. **Navigate to the Plugin Directory**:

   ```bash
   cd struck
   ```

3. **Install Dependencies**:

   Ensure you have [Node.js](https://nodejs.org/) installed, then run:

   ```bash
   npm install
   ```

4. **Initialize the Project**:

   ```bash
   npm run init
   ```

5. **Build the Plugin**:

   For development:

   ```bash
   npm run dev
   ```

   For production:

   ```bash
   npm run build
   ```

6. **Package the Plugin**:

   To create a zip file for installation:

   ```bash
   npm run package <zipName>
   ```

   Replace `<zipName>` with your desired file name.

7. **Activate the Plugin**:

   - Upload the generated zip file through the WordPress admin dashboard under Plugins > Add New > Upload Plugin.
   - Click "Install Now" and then "Activate".

## Usage

Once activated, struck will automatically start logging user actions.

Access the plugin's dashboard through the WordPress admin panel to view reports and configure settings.

## Development

This plugin utilizes Vue 3 and Tailwind CSS.

To avoid style conflicts with your WordPress theme, all Tailwind classes are prefixed with `tw-`.

You can modify this prefix in the `tailwind.config.js` file if necessary.

## Contributing

Contributions are welcome!

Please fork the repository and create a pull request with your changes.

## License

This project is licensed under the MIT License.

## Acknowledgements

This plugin is inspired by the vigilance of Sauron, the all-seeing antagonist from J.R.R. Tolkien's Middle-earth legendarium.

Just as Sauron kept a watchful eye over Middle-earth, this plugin helps you monitor user activities on your WordPress site.
