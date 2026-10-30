using IceCreamShop.Desktop.Models;
using System.Windows;

namespace IceCreamShop.Desktop;

public partial class MainWindow : Window
{
    public MainWindow()
    {
        InitializeComponent();
        UserText.Text = $"Logged in as {App.Api.CurrentUser?.Name} ({App.Api.CurrentUser?.Role})";
    }

    private async void Window_Loaded(object sender, RoutedEventArgs e)
    {
        await LoadItemsAsync();
        await LoadCustomersAsync();
    }

    private async void ItemSearchButton_Click(object sender, RoutedEventArgs e)
    {
        await LoadItemsAsync();
    }

    private async Task LoadItemsAsync()
    {
        ItemErrorText.Text = "";
        try
        {
            ItemsGrid.ItemsSource = await App.Api.GetItemsAsync(ItemSearchBox.Text);
        }
        catch (Exception ex)
        {
            ItemErrorText.Text = ex.Message;
        }
    }

    private async void AddItemButton_Click(object sender, RoutedEventArgs e)
    {
        var dialog = new ItemDialog { Owner = this };
        dialog.ShowDialog();
        await LoadItemsAsync();
    }

    private async void EditItemButton_Click(object sender, RoutedEventArgs e)
    {
        if (ItemsGrid.SelectedItem is not Item item)
        {
            ItemErrorText.Text = "Select an item to edit first.";
            return;
        }

        var dialog = new ItemDialog(item) { Owner = this };
        dialog.ShowDialog();
        await LoadItemsAsync();
    }
    private async void CustomerSearchButton_Click(object sender, RoutedEventArgs e)
    {
        await LoadCustomersAsync();
    }

    private async Task LoadCustomersAsync()
    {
        CustomerErrorText.Text = "";
        try
        {
            CustomersGrid.ItemsSource = await App.Api.GetCustomersAsync(CustomerSearchBox.Text);
        }
        catch (Exception ex)
        {
            CustomerErrorText.Text = ex.Message;
        }
    }

    private async void AddCustomerButton_Click(object sender, RoutedEventArgs e)
    {
        var dialog = new CustomerDialog { Owner = this };
        dialog.ShowDialog();
        await LoadCustomersAsync();
    }

    private async void EditCustomerButton_Click(object sender, RoutedEventArgs e)
    {
        if (CustomersGrid.SelectedItem is not Customer customer)
        {
            CustomerErrorText.Text = "Select a customer to edit first.";
            return;
        }

        var dialog = new CustomerDialog(customer) { Owner = this };
        dialog.ShowDialog();
        await LoadCustomersAsync();
    }
}