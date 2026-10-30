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
}